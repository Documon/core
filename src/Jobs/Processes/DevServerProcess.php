<?php

namespace Documon\Jobs\Processes;

use Closure;
use Documon\Jobs\Helpers\RendererHelper;
use Documon\RendererServiceProvider;
use Exception;
use Documon\Jobs\Helpers\MessageHelper;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

class DevServerProcess implements ProcessInterface
{
    use MessageHelper, RendererHelper;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $host = 'localhost';

    /**
     * @var string
     */
    private $port = '8080';

    /**
     * @var string
     */
    private $workDir = '';

    /**
     * @var RendererServiceProvider
     */
    private $renderer;

    /**
     * Spinner constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->workDir = $config['work-dir'];
        $this->renderer = $this->createRenderer($config);
    }

    /**
     * @param LoopInterface $loop
     *
     * @return void
     */
    public function execute(LoopInterface $loop)
    {
        $serveCommand = $this->renderer->serveCommand();
        $command = 'cd ' . $this->workDir . ' && ' . $serveCommand;
        $command .= ' --host ' . $this->host;
        $command .= ' --port ' . $this->port;

        $this->process = new Process($command);
        $this->process->start($loop);

        $this->process->stdout->on('data', function ($chunk) {
            preg_match('#Server running at (.+)#', $chunk, $matches);

            if (isset($matches[1])) {
                $this->info("Starting preview server on " . $matches[1]);
            } else {
                $this->debug($chunk);
            }
        });

        $this->process->stdout->on('error', function (Exception $e) {
            $this->error('error: ' . $e->getMessage());
        });
    }

    /**
     * @return Closure
     */
    public function getTerminationListener(): Closure
    {
        return function (int $signal) {
            $this->process->terminate($signal);
        };
    }
}
