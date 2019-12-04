<?php

namespace Documon\Jobs\Processes;

use AlecRabbit\Snake\Contracts\SpinnerInterface;
use AlecRabbit\Snake\Spinner;
use Closure;
use Documon\Jobs\Helpers\MessageHelper;
use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;
use React\EventLoop\LoopInterface;
use RuntimeException;

class FileWatcherProcess implements ProcessInterface
{
    use MessageHelper;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Watcher
     */
    private $watcher;

    /**
     * @var SpinnerInterface
     */
    private $spinner;

    /**
     * @var Closure
     */
    private $buildHandler;

    /**
     * FileWatcherJob constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->watcher = new Watcher(new Tracker(), new Filesystem());
    }

    /**
     * @param Closure $handler
     *
     * @return self
     */
    public function setBuildHandler(Closure $handler): self
    {
        $this->buildHandler = $handler;

        return $this;
    }

    /**
     *
     * @param LoopInterface $loop
     *
     * @return void
     */
    public function execute(LoopInterface $loop)
    {
        $filename = $this->config['filename'];
        $this->buildContent($filename);

        $loop->addTimer(1, function () use ($filename) {
            try {
                $this->spinner = new Spinner();
                $listener = $this->watcher->watch($filename);

                $listener->onModify(function ($resource, $path) {
                    $this->debug("{$path} has been modified.");
                    $this->buildContent($path);
                });

                $this->spinner->begin();

                $this->watcher->start(
                    $this->spinner->interval() * 1000000,
                    null,
                    function () {
                        $this->spinner->spin();
                    }
                );
            } catch (RuntimeException $exception) {
                $this->error("File: $filename does not exist!");
                exit(1);
            }
        });
    }

    /**
     * @param string $path
     */
    public function buildContent(string $path): void
    {
        $config = array_merge([
            'filename' => $path,
        ], $this->config);

        ($this->buildHandler)($config);
    }

    /**
     * @return Closure
     */
    public function getTerminationListener(): Closure
    {
        return function () {
            $this->spinner->end();
            $this->watcher->stop();
        };
    }
}
