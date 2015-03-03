<?php namespace App\Http\Controllers;

use Log;
use Debugbar;
use App\Plate;
use Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Cookie\CookieJar;
use App\Exceptions\ApiErrorException;
use App\Exceptions\ApiWarningException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

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
     * @throws ApiErrorException
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
     * @return ApiWarningException|\Symfony\Component\HttpFoundation\Response
     * @throws ApiErrorException
     */
	public function _waRegoCheck($plate, $ajax = true) {
        // Do a quick search to see if we've already successfully crawled this plate
        $existingPlate = Plate::withTrashed()->where('plate', '=', $plate, 'and')->where('status', '=', 2)->get();

        if (count($existingPlate) > 0) {
            // Plate exists!
            if ($existingPlate->first()->trashed()) {
                Log::info(sprintf('Trashed plate %s searched for', $plate));

                if ($ajax) {
                    return response()->json([
                        'status' => 'error',
                        'message' => sprintf('Previously invalid plate %s scraped', $plate)
                    ]);
                } else {
                    return [
                        'status' => 'error',
                        'message' => sprintf('Previously invalid plate %s scraped', $plate)
                    ];
                }
            }

            $lastSearch = Carbon::createFromFormat('Y-m-d H:i:s', $existingPlate->first()->last_searched);
            $now = Carbon::now();

            // If the successful crawl was in the last 24 hours, we'll use it, otherwise, continue and move to normal crawl
            if ($lastSearch->diffInHours($now) <= 24) {
                Log::info(sprintf('Used cache for plate %s', $existingPlate->first()->plate));

                if ($ajax) {
                    return response()->json([
                        'status' => 'success',
                        'message' => sprintf('Plate expires on %s', $existingPlate->first()->status_text)
                    ]);
                } else {
                    return [
                        'status' => 'success',
                        'message' => sprintf('Plate expires on %s', $existingPlate->first()->status_text)
                    ];
                }
            }
        }

        // Set up the cookies *cookie monster voice*
        $cookieJar = new CookieJar();

        // Get form page and cookie
        $sessionResponse = $this->webClient->get('https://online.transport.wa.gov.au/webExternal/registration/', [
            'cookies' => $cookieJar
        ]);

		$sessionBody = $sessionResponse->getBody();

		$sessionSite = new Crawler($sessionBody->getContents());

        // Get the action property from the form element
		$sessionUrlRaw = substr($sessionSite->filter('div.licensing-big-form form')->attr('action'), 1);

        // The action property should contain a Session ID and form query string for our matching Session ID
		if (!preg_match('#jsessionid#', $sessionUrlRaw)) {
			throw new ApiErrorException('Did not receive valid session', 500);
		}

        // Need to grab the form query string parameter, split the action and grab the 2nd half
        $sessionUrl = explode('?', $sessionUrlRaw);

		$apiResponse = null;

		try {
			$apiResponse = $this->apiClient->post('https://online.transport.wa.gov.au/webExternal/registration/?' . $sessionUrl[1], [
				'query' => [
                    'plate' => $plate,
                ],
                'cookies' => $cookieJar
			]);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
                // Unknown error with POST to API, log and alert
				Log::error($e->getResponse());
                throw new ApiErrorException('Unknown query error occurred', 500);
			}
		}

        // API Response should be an object, if it isn't, throw ApiErrorException
		if (is_object($apiResponse)) {
			$apiResponseBody = $apiResponse->getBody();

			$apiExpiryBody = new Crawler($apiResponseBody->getContents());

            // Crawl API Response page for the form response data
			$expiryResults = $apiExpiryBody->filter('div.licensing-big-form .data')->eq(1);

            // There should be at least 1 result in the $expiryResults DomCrawler filter
			if (count($expiryResults) == 0) {
                // If the response didn't match what we were expecting, the plate has been returned because it doesn't exist
                if ($apiExpiryBody->filter('.section-body p strong span')->count()) {
                    throw new ApiErrorException(sprintf('Unable to locate plate "%s"', $plate), 500);
                } else {
                    // If we made it to here, the response we got was completely messed up, abort!
                    throw new ApiErrorException('Unable to scrape registration details from DoT', 500);
                }
			} else {
				$expiryResults = $expiryResults->text();
			}

            // Response string should either contain a date or the unregistered keyword
			if (preg_match('#unregistered#', $expiryResults)) {
                return new ApiWarningException(sprintf('Plate "%s" is unregistered, expired, suspended or cancelled', $plate), 500);
            } elseif (!preg_match('/[0-3][0-9]\/[0-1][0-9]\/[1-2][0-9]{3}/i', $expiryResults)) {
                return new ApiWarningException('Invalid data scraped from DoT', 500);
            }

            Log::info('Successful search for in-date plate');
            // If we get this far, everything has succeeded

            if ($ajax) {
                return response()->json([
                    'status' => 'success',
                    'message' => sprintf('Plate expires on %s', $expiryResults)
                ]);
            } else {
                return [
                    'status' => 'success',
                    'message' => sprintf('Plate expires on %s', $expiryResults)
                ];
            }
		} else {
            // API Response wasn't an object, something pretty weird happened
            Log::error('Unknown error occurred with $apiResponse');
			throw new ApiErrorException('An unknown error occurred', 500);
		}
	}
}
