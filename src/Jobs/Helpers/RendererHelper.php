<?php

namespace Documon\Jobs\Helpers;

use Documon\RendererInterface;

trait RendererHelper
{
    /**
     * @var array
     */
    protected static $rendererMapping = [
        'default' => Example::class,
    ];

    /**
     * @param RendererInterface $renderer
     */
    protected function renderTemplate(RendererInterface $renderer): void
    {
        $renderer->render();
    }

    /**
     * @param array $config
     *
     * @return RendererInterface|null
     */
    protected function createRenderer(array $config): ?RendererInterface
    {
        // Custom renderer first
        $rendererClass = $config['engine'];

        if (
            class_exists($rendererClass)
            && is_subclass_of($rendererClass, RendererInterface::class)
        ) {
            return new $rendererClass($config);
        }

        // Use internal renderer
        $rendererType = $config['type'];

        return isset(static::$rendererMapping[$rendererType])
            ? new static::$rendererMapping[$rendererType]($config)
            : null;
    }
}
