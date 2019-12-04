<?php

namespace Documon\Commands;

use Documon\Commands\Helpers\JobHelper;
use Documon\Jobs\ServeJob;
use Illuminate\Console\Command;

class ServeCommand extends Command
{
    use JobHelper;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'serve 
                                {--host=localhost : Address to bind local preview server to}
                                {--port=8080 : Port for local preview server}
                                ' . AbstractCommand::JOB_ARGUMENTS_OPTIONS;

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start a local live preview server';

    /**
     * Execute the console command.
     *
     * @param ServeJob $job
     *
     * @return void
     */
    public function handle(ServeJob $job)
    {
        $this->setupJob($job)->run();

        $this->terminate(function () use ($job) {
            $job->terminate();
        });
    }
}
