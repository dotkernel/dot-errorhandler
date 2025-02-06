<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;
use Dot\ErrorHandler\Extra\Provider\CookieProvider;
use Dot\ErrorHandler\Extra\Provider\HeaderProvider;
use Dot\ErrorHandler\Extra\Provider\RequestProvider;
use Dot\ErrorHandler\Extra\Provider\ServerProvider;
use Dot\ErrorHandler\Extra\Provider\SessionProvider;
use Dot\ErrorHandler\Extra\Provider\TraceProvider;

use function array_key_exists;
use function class_exists;
use function is_bool;
use function is_string;

class ExtraProvider
{
    private CookieProvider $cookie;
    private HeaderProvider $header;
    private RequestProvider $request;
    private ServerProvider $server;
    private SessionProvider $session;
    private TraceProvider $trace;

    public function __construct(array $options = [])
    {
        $extras = [
            'cookie'  => CookieProvider::class,
            'header'  => HeaderProvider::class,
            'request' => RequestProvider::class,
            'server'  => ServerProvider::class,
            'session' => SessionProvider::class,
            'trace'   => TraceProvider::class,
        ];

        foreach ($extras as $logKey => $logClass) {
            $enabled   = false;
            $processor = null;
            if (array_key_exists($logClass, $options)) {
                if (isset($options[$logClass]['enabled']) && is_bool($options[$logClass]['enabled'])) {
                    $enabled = $options[$logClass]['enabled'];
                }
                if (
                    isset($options[$logClass]['processor'])
                    && is_string($options[$logClass]['processor'])
                    && class_exists($options[$logClass]['processor'])
                ) {
                    $processor = new $options[$logClass]['processor']();
                    if (! $processor instanceof ProcessorInterface) {
                        $processor = null;
                    }
                }
            }

            $this->$logKey = new $logClass($enabled, $processor);
        }
    }

    public function getCookie(): CookieProvider
    {
        return $this->cookie;
    }

    public function getHeader(): HeaderProvider
    {
        return $this->header;
    }

    public function getRequest(): RequestProvider
    {
        return $this->request;
    }

    public function getServer(): ServerProvider
    {
        return $this->server;
    }

    public function getSession(): SessionProvider
    {
        return $this->session;
    }

    public function getTrace(): TraceProvider
    {
        return $this->trace;
    }
}
