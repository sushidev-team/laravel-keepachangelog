<?php

namespace AMBERSIVE\KeepAChangelog\Console\Commands\Dev;

use Illuminate\Console\Command;

use Artisan;
use Storage;

class KeepAChangelogRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'changelog:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all unreleased items in the change log file into the release state.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        

    }

}
