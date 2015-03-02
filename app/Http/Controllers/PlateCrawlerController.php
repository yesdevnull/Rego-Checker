<?php namespace App\Http\Controllers;

use Log;
use App\Plate;
use Controller;

/**
 * Class PlateCrawlerController
 * @package App\Http\Controllers
 */
class PlateCrawlerController extends Controller {
    public function searchPlate(Plate $plate) {
        $checkerController = new RegistrationController();

        switch ($plate->state) {
            case 'WA' :
                $result = $checkerController->_waRegoCheck($plate->plate, false);

                dd($result);
                if ($result['status'] == 'success') {

                }
            break;
        }
    }
}