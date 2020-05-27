<?php

namespace AMBERSIVE\KeepAChangelog\Console\Commands\Dev;

use Illuminate\Console\Command;

use AMBERSIVE\KeepAChangelog\Classes\ChangelogHelper;

use PHLAK\SemVer;

class KeepAChangelogRelease extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'changelog:release {--repository= : Define the repository. Default = default} {--semver= : Please provide a valid semver version number.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all unreleased items in the change log file into the release state.';

    protected $repository;

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

        $project = data_get($this->options(), 'repository', $this->repository) == null || in_array(data_get($this->options(), 'repository'), $repositories) === false ? 
                        sizeof($repositories) == 1 ? $repositories[0] :  $this->choice('Choose the repository', $repositories) : 
                        data_get($this->options(), 'repository');

        $semver = data_get($this->options(), 'semver') == null ? 
                        $this->ask('Please insert the semver number for the release (eg. "1.0.0")') : 
                        data_get($this->options(), 'semver');

        if ($semver === null || $semver === "") {
            $this->error('Please provide a valid semver. (Version number)');
            return $this->handle();
        }

        $version = SemVer\Version::parse($semver);

        $success = ChangelogHelper::release($project, $version->major, $version->minor, $version->patch);

        $success === true ? $this->line("Release for \"${project}\" in Version: ${version} has been created.") : 
                            $this->error("An error occured while trying to create a release entry for \"${project}\".");
        
    }

}
