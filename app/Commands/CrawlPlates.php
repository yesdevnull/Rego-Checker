<?php namespace App\Commands;

use Log;
use App\Plate;
use App\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use App\Http\Controllers\PlateCrawlerController;

class CrawlPlates extends Command implements SelfHandling, ShouldBeQueued {
	use InteractsWithQueue, SerializesModels;

    protected $plate;

    /**
     * @param Plate $plate
     */
	public function __construct(Plate $plate) {
        $this->plate = $plate;
    }

    /**
     * @param Plate $plate
     */
	public function handle() {
        $crawler = new PlateCrawlerController();

        $crawler->searchPlate($this->plate);
	}
}
