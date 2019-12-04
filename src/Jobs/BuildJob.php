<?php

namespace Documon\Jobs;

use Documon\RendererInterface;
use Exception;
use Documon\Jobs\Helpers\FileSystemHelper;
use Documon\Jobs\Helpers\RendererHelper;
use RuntimeException;

class BuildJob extends AbstractJob
{
    use RendererHelper, FileSystemHelper;

    /**
     * @return void
     */
    public function run(): void
    {
        try {
            $config = $this->readConfig('build');
            $renderer = $this->createRenderer($config);
            $this->checkOutputDirectory($config);
            $this->setupWorkDirectory($config);
            $this->renderTemplate($renderer);
            $this->runBuildCommand($renderer);
            $this->moveBuiltDirectory($config);
            $this->removeWorkDirectory();
        } catch (Exception $exception) {
            $this->errorHappened($exception);
        }
    }

    /**
     * @param array $config
     *
     * @return bool
     */
    protected function checkOutputDirectory(array $config)
    {
        $outputDir = $config['output'];
        if ($this->isDirectoryNotEmpty($outputDir)) {
            throw new RuntimeException("Path $outputDir is not empty");
        }

        return true;
    }

    /**
     * @param RendererInterface $renderer
     *
     * @return void
     */
    private function runBuildCommand(RendererInterface $renderer): void
    {
        $buildCommand = $renderer->buildCommand();
        $command = sprintf('cd %s && %s', $this->workDir, $buildCommand);
        $output = shell_exec($command);
        $this->debug($output);
    }

    /**
     * @param array $config
     */
    private function moveBuiltDirectory(array $config): void
    {
        $distPath = $this->workDir . '/dist';
        $outputPath = $config['output'];

        $this->debug("Output Directory: " . $outputPath);

        $this->fs->moveDirectory($distPath, $outputPath);
    }
}
