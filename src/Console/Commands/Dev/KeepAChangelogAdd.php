<?php

namespace AMBERSIVE\KeepAChangelog\Console\Commands\Dev;

use Illuminate\Console\Command;

use Artisan;
use Storage;

use Arr;

use AMBERSIVE\KeepAChangelog\Classes\ChangelogEnum;
use AMBERSIVE\KeepAChangelog\Classes\ChangelogHelper;

class KeepAChangelogAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'changelog:add {--repository= : Define the repository. Default = default} {--type= : Which type of changelog entry} {--text= : Describe the change}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a changelog entry.';

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

        $repositories = array_keys(config('keepachangelog.repositories', []));
        $repositories = empty($repositories) === true ? ['default'] : $repositories;

        $types = ChangelogEnum::TYPES;

        // Extract the repository from 
        $project = data_get($this->options(), 'repository') == null || in_array(data_get($this->options(), 'repository'), $repositories) === false ? 
                        sizeof($repositories) == 1 ? $repositories[0] :  $this->choice('Choose the repository', $repositories) : 
                        data_get($this->options(), 'repository');

        $type = data_get($this->options(), 'type') == null || in_array(data_get($this->options(), 'type'), $types) === false ? $this->choice('Choose the type of your changelog entry.', $types) :  data_get($this->options(), 'type');

        $text = data_get($this->options(), 'text') == null ? $this->ask('Please describe what has been changed.') :  data_get($this->options(), 'text');
        
        // Check if the change log file exists
        ChangelogHelper::prepare($project);

        // Insert the line
        $success = ChangelogHelper::addLine($project, $type, $text);

        $success === true ? $this->line("Line has been added to \"${project}\".") : 
                            $this->error("An error occured while trying to a line to \"${project}\".");

    }

}
