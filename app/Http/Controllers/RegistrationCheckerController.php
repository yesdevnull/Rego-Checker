<?php namespace App\Http\Controllers;

use Log;
use Debugbar;
use Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Exceptions\ApiErrorException;
use App\Exceptions\ApiWarningException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * Class RegistrationController
 * @package App\Http\Controllers
 */
class RegistrationController extends Controller {
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

    /**
     * @param Request $request
     * @return ApiWarningException|array
     */
    public function plateCheck(Request $request) {
        return $this->stateSwitch($request->input('state'), $request->input('plate'));
    }

    /**
     * @param $state
     * @param $plate
     * @return ApiWarningException|array
     * @throws ApiErrorException
     */
	public function stateSwitch($state, $plate) {
		switch ($state) {
			case 'wa' :
				return $this->_waRegoCheck($plate);
			break;

            default:
                throw new ApiErrorException('No State supplied', 500);
            break;
		}
	}

    /**
     * @param $plate
     * @return ApiWarningException|array
     * @throws ApiErrorException
     */
	public function _waRegoCheck($plate) {
		$sessionRequest = $this->webClient->createRequest('GET', 'https://online.transport.wa.gov.au/webExternal/registration/?');

		$sessionResponse = $this->webClient->send($sessionRequest);

		$sessionBody = $sessionResponse->getBody();

		$sessionSite = new Crawler($sessionBody->getContents());

		$sessionId = substr($sessionSite->filter('div.licensing-big-form form')->attr('action'), 1);

		if (!preg_match('#jsessionid#', $sessionId)) {
			throw new ApiErrorException('Did not receive valid session', 500);
		}

        Log::info('Session ID: ' . $sessionId);

		$apiResponse = '';

		try {
			$apiResponse = $this->apiClient->post('https://online.transport.wa.gov.au/webExternal/registration' . $sessionId, [
				'query' => ['plate' => $plate]
			]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				Log::error($e->getResponse());
                throw new ApiErrorException('Unknown query error occurred', 500);
			}
		}

		if (is_object($apiResponse)) {
			$apiResponseBody = $apiResponse->getBody();

			$apiExpiryBody = new Crawler($apiResponseBody->getContents());

			$expiryResults = $apiExpiryBody->filter('div.licensing-big-form .data')->eq(1);
            //$expiryResults = $apiExpiryBody->filter('div.licensing-big-form')->html();

            //dd($expiryResults);

			if (count($expiryResults) == 0) {
                if ($apiExpiryBody->filter('.section-body p strong span')->count()) {
                    $apiExpiryBody->filter('.section-body p strong span')->first()->text();

                    throw new ApiErrorException(sprintf('Unable to locate plate "%s"', $plate), 500);
                } else {
                    throw new ApiErrorException('Unable to scrape registration details from DoT', 500);
                }
			} else {
				$expiryResults = $expiryResults->text();
			}

			if (preg_match('#unregistered#', $expiryResults)) {
                return new ApiWarningException(sprintf('Plate "%s" is unregistered, expired, suspended or cancelled', $plate), 500);
            } elseif (!preg_match('/[0-3][0-9]\/[0-1][0-9]\/[1-2][0-9]{3}/i', $expiryResults)) {
                return new ApiWarningException('Invalid data scraped from DoT', 500);
            }

            Debugbar::info('Success, plate expires ' . $expiryResults);

            return response()->json([
                'status' => 'success',
                'message' => sprintf('Plate expires on %s', $expiryResults)
            ]);
		} else {
			throw new ApiErrorException('An unknown error occurred', 500);
		}
	}
}
