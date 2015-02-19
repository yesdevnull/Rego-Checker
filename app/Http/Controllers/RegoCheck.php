<?php

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class RegoCheck
 */
class RegoCheck extends Controller {
	/**
	 * @var
	 */
	protected $webClient;

	/**
	 * @var
	 */
	protected $apiClient;

    /**
     * Constructor
     */
	public function __construct() {
		$this->webClient = new Client();

		$this->apiClient = new Client();
	}

    public function plateCheck($state, $plate) {
        return $this->stateSwitch($state, $plate);
    }

	/**
	 * @param string $state
	 * @param string $plate
	 * @return string|Crawler
	 * @throws Exception
	 */
	public function stateSwitch($state, $plate) {
		switch ($state) {
			case 'wa' :
				return $this->_waRegoCheck($plate);
			break;
		}
	}

	/**
	 * @param string $plate
	 * @return string|Crawler
	 * @throws Exception
	 */
	public function _waRegoCheck($plate) {
		$sessionRequest = $this->webClient->createRequest('GET', 'https://online.transport.wa.gov.au/webExternal/registration/?');

		$sessionResponse = $this->webClient->send($sessionRequest);

		$sessionBody = $sessionResponse->getBody();

		$sessionSite = new Crawler($sessionBody->getContents());

		$sessionId = substr($sessionSite->filter('div.licensing-big-form form')->attr('action'), 1);

		if (!preg_match('#jsessionid#', $sessionId)) {
			throw new Exception('Did not receive valid session');
		}

		$apiResponse = '';

		try {
			$apiResponse = $this->apiClient->post('https://online.transport.wa.gov.au/webExternal/registration' . $sessionId, [
				'query' => ['plate' => $plate]
			]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				echo $e->getResponse() . "\n";
			}
		}

		if (is_object($apiResponse)) {
			$apiResponseBody = $apiResponse->getBody();

			$apiExpiryBody = new Crawler($apiResponseBody->getContents());

			$expiryResults = $apiExpiryBody->filter('div.licensing-big-form .data')->eq(1);

			if (count($expiryResults) == 0) {
				if ($apiExpiryBody->filter('.section-body p strong span')->first()->text()) {
                    return ['status' => 'error', 'message' => sprintf('Unable to locate plate %s', $plate)];
				} else {
                    return ['status' => 'error', 'message' => 'Unable to scrape registration details from DoT'];
				}
			} else {
				$expiryResults = $expiryResults->text();
			}

			if (preg_match('#unregistered#', $expiryResults)) {
                return ['status' => 'warning', 'message' => sprintf('Plate "%s" is unregistered, expired, suspended or cancelled', $plate)];
            } elseif (!preg_match('/[0-3][0-9]\/[0-1][0-9]\/[1-2][0-9]{3}/i', $expiryResults)) {
                return ['status' => 'warning', 'message' => 'Invalid data scraped from DoT'];
            }

			return ['status' => 'success', 'message' => sprintf('Plate expires on %s', $expiryResults)];
		} else {
			throw new Exception('An unknown error occurred');
		}
	}
}
