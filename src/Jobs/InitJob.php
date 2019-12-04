<?php

namespace Documon\Jobs;

use Documon\Jobs\Helpers\FileSystemHelper;
use Documon\Renderer\Example;
use Documon\RendererInterface;
use Exception;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class InitJob extends AbstractJob
{
    use FileSystemHelper;

    /**
     * @var string
     */
    protected $engine = Example::class;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $destDir = '';

    /**
     * @return void
     */
    public function run(): void
    {
        try {
            $this->checkEngine();
            $this->setUp();
            $this->copyTemplateFile();
            $this->generateConfigFile($this->getConfigInYamlFormat());
        } catch (Exception $exception) {
            $this->errorHappened($exception);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkEngine()
    {
        $this->engine = $this->arguments['engine'];

        if (!is_subclass_of($this->engine, RendererInterface::class)) {
            throw new InvalidArgumentException("{$this->engine} should implement " . RendererInterface::class);
        }
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->fs = new Filesystem();
        $this->destDir = getcwd() . '/templates';
    }

    /**
     * @return void
     */
    private function copyTemplateFile(): void
    {
        if ($this->isDirectoryNotEmpty($this->destDir)) {
            throw new RuntimeException("Path $this->destDir is not empty");
        }

        $sourceDir = $this->engine::template();
        if (is_dir($sourceDir)) {
            $this->destDir .= '/' . $this->engine::type();
        } else {
            $sourceDir = __DIR__ . '/../../templates';
        }

        if ($this->fs->copyDirectory($sourceDir, $this->destDir)) {
            $this->echo("<comment>Template files are created in {$this->destDir}</comment>");
        } else {
            throw new RuntimeException("Template files can't be created on {$this->destDir}");
        }
    }

    /**
     * @return string
     */
    private function getConfigInYamlFormat(): string
    {
        $type = $this->engine::type();
        $options = $this->engine::options();

        $config = [
            $type => [
                'renderer' => [
                    'template' => './templates/' . $type,
                    'engine' => $this->engine,
                    'options' => $options,
                ],
            ],
        ];

        return Yaml::dump($config, 10);
    }

    /**
     * @param string $yaml
     */
    private function generateConfigFile(string $yaml): void
    {
        $filename = getcwd() . '/' . AbstractJob::CONFIG_FILE;

        if (!file_exists($filename)) {
            file_put_contents($filename, $yaml);
        } else {
            file_put_contents($filename, $yaml, FILE_APPEND);
        }

        $this->echo("<comment>Config file $filename is created</comment>");
    }
}
