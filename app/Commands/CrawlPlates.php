<?php namespace App\Commands;

use Queue;
use App\Plate;
use App\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use App\Http\Controllers\PlateCrawlerController;

class CrawlPlates extends Command implements SelfHandling, ShouldBeQueued {
	use InteractsWithQueue, SerializesModels;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
        //
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle() {
		$plates = Plate::all();
        $crawler = new PlateCrawlerController();

        $plates->each(function($plate) use ($crawler) {
            $crawler->searchPlate($plate);

            Queue::push($crawler->searchPlate($plate));
        });
	}
}
