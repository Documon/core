<?php

namespace Documon\Commands\Helpers;

use Documon\Jobs\AbstractJob;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Event;

/**
 * @property OutputStyle $output
 */
trait JobHelper
{
    /**
     * @param AbstractJob $job
     *
     * @return AbstractJob
     */
    public function setupJob(AbstractJob $job): AbstractJob
    {
        return $job
            ->setOptionDefaults($this->getDefinition()->getOptionDefaults())
            ->setArguments($this->arguments())
            ->setOptions($this->options())
            ->setInfoHandler(function ($message) {
                $this->info($message, OutputStyle::VERBOSITY_NORMAL);
            })
            ->setEchoHandler(function ($message) {
                $this->line($message, false, OutputStyle::VERBOSITY_NORMAL);
            })
            ->setDebugHandler(function ($message) {
                $this->line($message, null, OutputStyle::VERBOSITY_DEBUG);
            })
            ->setErrorHandler(function ($message) {
                $this->error($message, OutputStyle::VERBOSITY_NORMAL);
            });
    }

    /**
     * @param callable $callback
     */
    public function terminate(callable $callback): void
    {
        Event::listen(CommandFinished::class, $callback);
    }
}
