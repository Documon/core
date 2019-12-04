<?php

namespace Documon\Commands;

use Documon\Commands\Helpers\JobHelper;
use Documon\Jobs\InitJob;
use Documon\Renderer\Example;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    use JobHelper;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install
                                {engine : Fully-qualified class name of renderer engine, ex: ' . Example::class . ' }';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install renderer template and set config';

    /**
     * Execute the console command.
     *
     * @param InitJob $job
     *
     * @return void
     */
    public function handle(InitJob $job)
    {
        $this->setupJob($job)->run();

        $this->terminate(function () use ($job) {
            $job->terminate();
        });
    }
}
