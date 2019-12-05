<?php

namespace Documon\Jobs\Helpers;

use Documon\RendererInterface;
use RuntimeException;

trait RendererHelper
{
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

        throw new RuntimeException("No renderer engine be defined");
    }
}
