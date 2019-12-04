<?php

namespace Documon\Commands;

use Documon\Jobs\AbstractJob;

abstract class AbstractCommand
{
    public const JOB_ARGUMENTS_OPTIONS = '{filename : Input document}
                                {--c|config=' . AbstractJob::CONFIG_FILE . ' : Path of configuration file}
                                {--t|type= : Document type}
                                {--e|engine= : Fully-qualified class name of renderer engine}
                                {--template= : Path to custom template}
                                {--w|work-dir= : Writable temporary-directory for front-end assets building}';
}
