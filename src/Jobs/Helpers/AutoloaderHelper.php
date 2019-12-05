<?php

namespace Documon\Jobs\Helpers;

use RuntimeException;

trait AutoloaderHelper
{
    protected function autoload()
    {
        $autoloaderPath = getcwd() . '/vendor/autoload.php';

        if (!file_exists($autoloaderPath)) {
            throw new RuntimeException("No Composer-based project in current path");
        }

        include_once $autoloaderPath;
    }
}