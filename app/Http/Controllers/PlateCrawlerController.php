<?php namespace App\Http\Controllers;

use Bus;
use Log;
use App\Plate;
use Controller;
use Carbon\Carbon;
use App\Commands\CrawlPlates;
use Illuminate\Foundation\Bus\DispatchesCommands;

/**
 * Class PlateCrawlerController
 * @package App\Http\Controllers
 */
class PlateCrawlerController extends Controller {
    use DispatchesCommands;

    public function initiateCrawler() {
        $plates = Plate::all();

        $that = $this;

        Log::info('Loaded plates models, getting ready to dispatch');

        $plates->each(function($plate) use ($that) {
            Log::info(sprintf('handling dispatch for plate %s', $plate->plate));

            $that->dispatch(
                new CrawlPlates($plate)
            );
        });

        Log::info('dispatched all plates and crawlers');
    }

    public function searchPlate(Plate $plate) {
        $checkerController = new RegistrationController();
        $notificationController = new NotificationController();

        switch ($plate->state) {
            case 'WA' :
                $result = $checkerController->_waRegoCheck($plate->plate, false);

                if ($result['status'] == 'success') {
                    preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/i', $result['message'], $date);

                    $plate->status_text = $date[0];
                    $plate->status = $plate->status_list[$result['status']];

                    // If the plate has less than 30 days until it expires, alert the email addresses assigned to that plate
                    if ($notificationController->checkDates($date[0])) {
                        $notificationController->sendAlertEmail($plate);
                    }

                    Log::info(sprintf('In-date status for plate %s', $plate->plate));
                } elseif ($result['status'] == 'warning' && preg_match('#unregistered#i', $result['message'])) {
                    $plate->status_text = $result['message'];
                    $plate->status = $plate->status_list['unregistered'];

                    Log::info(sprintf('Unregistered status for plate %s', $plate->plate));
                } elseif ($result['status' == 'error']) {
                    if (preg_match('#unknown#i', $result['message'])) {
                        $plate->status_text = $result['message'];
                        $plate->status = $plate->status_list[0];

                        Log::info(sprintf('Unknown status for plate %s', $plate->plate));
                    } elseif (preg_match('#unable to locate#i', $result['message'])) {
                        if ($plate->status == 99) {
                            // Plate was invalid yesterday, and is again invalid after this search - delete time

                            Log::info(sprintf('Plate %s failed 2 days in a row, deleting', $plate->plate));

                            $plate->delete();

                            return true;
                        }
                    }
                }

                $plate->last_searched = Carbon::now()->toDateTimeString();
                $plate->save();

                Log::info(sprintf('Finished crawling plate %s', $plate->plate));
            break;
        }
    }
}