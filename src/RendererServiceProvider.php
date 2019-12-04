<?php

namespace Documon;

use Illuminate\Support\ServiceProvider;
use Documon\Commands;

class RendererServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Commands\InstallCommand::class,
            Commands\BuildCommand::class,
            Commands\ServeCommand::class,
        ]);
    }
}