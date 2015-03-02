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

//            Bus::dispatch(
//                new CrawlPlates($plate)
//            );

            $that->dispatch(
                new CrawlPlates($plate)
            );
        });

        Log::info('dispatched all plates and crawlers');
    }

    public function searchPlate(Plate $plate) {
        $checkerController = new RegistrationController();

        switch ($plate->state) {
            case 'WA' :
                $result = $checkerController->_waRegoCheck($plate->plate, false);

                //dd($result);
                if ($result['status'] == 'success') {
                    preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/i', $result['message'], $date);

                    $plate->status_text = $date[0];
                    $plate->status = $plate->status_list[$result['status']];
                    $plate->last_searched = Carbon::now()->toDateTimeString();

                    $plate->save();
                }
            break;
        }
    }
}