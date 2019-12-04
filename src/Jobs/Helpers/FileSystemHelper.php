<?php

namespace Documon\Jobs\Helpers;

use FilesystemIterator;

trait FileSystemHelper
{
    /**
     * @param string $dir
     *
     * @return bool
     */
    protected function isDirectoryNotEmpty(string $dir): bool
    {
        return is_dir($dir) && (new FilesystemIterator($dir))->valid();
    }
}
