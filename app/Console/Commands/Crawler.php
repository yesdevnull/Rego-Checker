<?php namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use App\Http\Controllers\PlateCrawlerController;
use Symfony\Component\Console\Input\InputArgument;

class Crawler extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the Plate crawler';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Running crawler...');

        $crawler = new PlateCrawlerController();

        $crawler->initiateCrawler();
    }
}
