<?php

namespace Documon\Jobs;

use Closure;
use Exception;
use Documon\Jobs\Helpers\MessageHelper;
use Documon\Jobs\Helpers\RendererHelper;
use Documon\Jobs\Processes\DevServerProcess;
use Documon\Jobs\Processes\FileWatcherProcess;
use Documon\Jobs\Processes\ProcessInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class ServeJob extends AbstractJob
{
    use RendererHelper;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @return void
     */
    public function run(): void
    {
        try {
            $config = $this->readConfig('serve');
            $this->setupWorkDirectory($config);
            $this->executeChildProcesses($config);
        } catch (Exception $exception) {
            $this->errorHappened($exception);
        }
    }

    /**
     * @param array $config
     */
    protected function executeChildProcesses(array $config): void
    {
        $this->loop = Factory::create();

        foreach ($this->processes($config) as $process) {
            $this->setMessageHandlers($process);
            $this->addTerminationListeners($process->getTerminationListener());
            $process->execute($this->loop);
        };

        $this->addTerminationListeners(function (int $signal) {
            $this->removeWorkDirectory();
            exit($signal);
        });

        $this->loop->run();
    }

    /**
     * @param array $config
     *
     * @return ProcessInterface[]
     */
    protected function processes(array $config): array
    {
        return [
            new DevServerProcess($config),
            (new FileWatcherProcess($config))->setBuildHandler(function () use ($config) {
                $this->createRenderer($config)->render();
            }),
        ];
    }

    /**
     * @param MessageHelper $target
     */
    protected function setMessageHandlers($target)
    {
        $target
            ->setInfoHandler(function ($message) {
                $this->info($message);
            })
            ->setEchoHandler(function ($message) {
                $this->echo($message);
            })
            ->setDebugHandler(function ($message) {
                $this->debug($message);
            })
            ->setErrorHandler(function ($message) {
                $this->error($message);
            });
    }

    /**
     * @param Closure $func
     */
    protected function addTerminationListeners(Closure $func): void
    {
        $this->loop->addSignal(SIGINT, $func);
        $this->loop->addSignal(SIGTERM, $func);
    }
}
