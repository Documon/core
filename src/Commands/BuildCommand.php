<?php

namespace Documon\Commands;

use Documon\Commands\Helpers\JobHelper;
use Documon\Jobs\BuildJob;
use Illuminate\Console\Command;

class BuildCommand extends Command
{
    use JobHelper;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build
                                {--o|output=./dist : Output path}
                                ' . AbstractCommand::JOB_ARGUMENTS_OPTIONS;

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build HTML from inputted documents';

    /**
     * Execute the console command.
     *
     * @param BuildJob $job
     *
     * @return void
     */
    public function handle(BuildJob $job)
    {
        $this->setupJob($job)->run();

        $this->terminate(function () use ($job) {
            $job->terminate();
        });
    }
}
