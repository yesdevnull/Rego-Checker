<?php

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RegoCheck extends Controller {
	/**
	 * @var
	 */
	protected $webClient;

	/**
	 * @var
	 */
	protected $apiClient;

	public function __construct() {
		$this->webClient = new Client();

		$this->apiClient = new Client();
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
					throw new Exception('Unable to locate plate: ' . $plate);
				} else {
					throw new Exception('An unknown error occurred');
				}
			} else {
				$expiryResults = $expiryResults->text();
			}

			if (preg_match('#unregistered#', $expiryResults)) {
                throw new Exception(sprintf('Plate "%s" is unregistered, expired, suspended or cancelled', $plate));
            } elseif (!preg_match("/[0-3][0-9]\/[0-1][0-9]\/[1-2][0-9]{3}/i", $expiryResults)) {
                throw new Exception('Invalid date returned');
            }

			return $expiryResults;
		} else {
			throw new Exception('An unknown error occurred');
		}
	}
}
