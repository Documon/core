<?php

namespace Documon\Jobs\Helpers;

trait MessageHelper
{
    /**
     * @var callable
     */
    protected $infoHandler;

    /**
     * @var callable
     */
    protected $echoHandler;

    /**
     * @var callable
     */
    protected $debugHandler;

    /**
     * @var callable
     */
    protected $errorHandler;

    /**
     * MessageHelper constructor.
     */
    public function __construct()
    {
        $this->infoHandler = function ($message) {
            echo $message;
        };

        $this->echoHandler = function ($message) {
            echo $message;
        };

        $this->debugHandler = function ($message) {
            echo $message;
        };

        $this->errorHandler = function ($message) {
            echo $message;
        };
    }

    /**
     * @param callable $handler
     *
     * @return static
     */
    public function setInfoHandler(callable $handler)
    {
        $this->infoHandler = $handler;

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return static
     */
    public function setEchoHandler(callable $handler)
    {
        $this->echoHandler = $handler;

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return static
     */
    public function setDebugHandler(callable $handler)
    {
        $this->debugHandler = $handler;

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return static
     */
    public function setErrorHandler(callable $handler)
    {
        $this->errorHandler = $handler;

        return $this;
    }

    /**
     * @param string $string
     */
    public function info(string $string): void
    {
        ($this->infoHandler)($string);
    }

    /**
     * @param string $string
     */
    public function echo(string $string): void
    {
        ($this->echoHandler)($string);
    }

    /**
     * @param string $message
     */
    public function debug(string $message): void
    {
        ($this->debugHandler)($message);
    }

    /**
     * @param string $message
     */
    public function error(string $message): void
    {
        ($this->errorHandler)($message);
    }
}
