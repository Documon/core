<?php

namespace Documon\Jobs;

use Exception;
use Documon\Jobs\Helpers\ConfigHelper;
use Documon\Jobs\Helpers\MessageHelper;
use Documon\Jobs\Helpers\WorkDirectoryHelper;

abstract class AbstractJob
{
    use ConfigHelper, WorkDirectoryHelper, MessageHelper;

    public const CONFIG_FILE = '.documon.yml';

    /**
     * @var bool
     */
    protected $hasError = false;

    /**
     * @return void
     */
    abstract public function run(): void;

    /**
     * @param Exception $exception
     *
     * @return void
     */
    protected function errorHappened(Exception $exception): void
    {
        $this->hasError = true;
        $this->error($exception->getMessage());
    }

    /**
     * @return void
     */
    public function terminate(): void
    {
        if (!$this->hasError) {
            $this->info('Done');
        }
    }
}
