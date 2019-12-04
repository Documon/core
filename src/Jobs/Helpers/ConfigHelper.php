<?php

namespace Documon\Jobs\Helpers;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

trait ConfigHelper
{
    /**
     * @var array
     */
    protected $optionDefaults = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $optionDefaults
     *
     * @return static
     */
    public function setOptionDefaults(array $optionDefaults)
    {
        $this->optionDefaults = $optionDefaults;

        return $this;
    }

    /**
     * @param array $arguments
     *
     * @return static
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return static
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function readConfig(string $name): array
    {
        $configFile = $this->options['config'];

        $parsedConfig = $this->parseConfigFile($configFile);

        $type = $this->options['type'] ?? 'default';

        if (!array_key_exists($type, $parsedConfig)) {
            throw new InvalidArgumentException("Type '$type' does not exist in config file");
        }

        return $this->convertConfig(array_merge(
            $this->optionDefaults,
            (array) (array) ($parsedConfig[$type]['renderer'] ?? []),
            (array) ($config[$name] ?? []),
            $this->arguments,
            array_diff($this->options, $this->optionDefaults)
        ));
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function parseConfigFile(string $path): array
    {
        if ($path !== self::CONFIG_FILE && !file_exists($path)) {
            throw new RuntimeException("Configuration file does not exist");
        }

        $content = file_get_contents($path);

        return (array) Yaml::parse($content);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function convertConfig(array $config): array
    {
        $newConfig = [];
        foreach ($config as $name => $value) {
            $newConfig[$name] = $this->convertValue($name, $value);
        }

        return $newConfig;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function convertValue(string $name, $value)
    {
        switch ($name) {
            case 'output':
                return $this->getOutputDirectory($value);
            case 'template';
                return $this->getTemplateDirectory($value);
            case 'work-dir':
                return $this->newWorkDir($value);
            default:
                return $value;
        }
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function getOutputDirectory($value): string
    {
        return $value ?: getcwd() . '/dist';
    }

    /**
     * @param string $template
     *
     * @return string|null
     */
    protected function getTemplateDirectory(?string $template): string
    {
        static $templatePath = __DIR__ . '/../../../templates/';

        if (!$template) {
            throw new InvalidArgumentException("Set the path of template first");
        }

        // Custom path first
        if (is_dir($template)) {
            return $template;
        }

        $internalTemplatePath = $templatePath . $template;

        if (is_dir($internalTemplatePath)) {
            return $internalTemplatePath;
        }

        throw new InvalidArgumentException("Template $template does not exist");
    }

    /**
     * @param string|null $workDir
     *
     * @return string
     */
    protected function newWorkDir(?string $workDir): string
    {
        $path = $workDir ?: getcwd() . '/.work';

        return $path . '/.' . hash('crc32', time());
    }
}
