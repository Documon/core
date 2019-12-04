<?php

namespace Documon\Jobs\Processes;

use Closure;
use React\EventLoop\LoopInterface;

interface ProcessInterface
{
    /**
     * @param LoopInterface $loop
     *
     * @return void
     */
    public function execute(LoopInterface $loop);

    /**
     * @return Closure
     */
    public function getTerminationListener(): Closure;
}
