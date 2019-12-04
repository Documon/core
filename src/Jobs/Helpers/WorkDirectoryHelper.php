<?php

namespace Documon\Jobs\Helpers;

use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;

/**
 * Trait EnvHelper
 * @package App\CommandHelpers
 * @property OutputStyle $output
 */
trait WorkDirectoryHelper
{
    /**
     * @var string
     */
    protected $workDir = '';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @param array $config
     */
    protected function setupWorkDirectory(array $config): void
    {
        $this->workDir = $config['work-dir'];
        $this->fs = new Filesystem();

        if (!$this->checkWorkDirectory()) {
            $this->initWorkDirectory($config);
        }
    }

    /**
     * @return bool
     */
    protected function checkWorkDirectory(): bool
    {
        $this->debug("Work Directory: " . $this->workDir);

        return is_dir($this->workDir);
    }

    /**
     * Create dirs and files
     *
     * @param array $config
     */
    protected function initWorkDirectory(array $config): void
    {
        $sourceDir = $config['template'];
        $destDir = $this->workDir;

        $this->fs->copyDirectory($sourceDir, $destDir);

        $initCommand = $config['init'] ?? 'yarn install';
        $fullCommand = sprintf('cd %s && %s 2>&1', $this->workDir, $initCommand);
        $output = shell_exec($fullCommand);

        $this->debug($output);
    }

    /**
     * @return void
     */
    protected function removeWorkDirectory(): void
    {
        $this->fs->deleteDirectory($this->workDir);
    }
}
