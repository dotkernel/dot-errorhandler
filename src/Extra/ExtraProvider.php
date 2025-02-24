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

use function array_flip;
use function array_key_exists;
use function array_map;
use function assert;
use function class_exists;
use function count;
use function is_array;
use function is_string;

class ExtraProvider
{
    public const CONFIG_KEY = 'extraProvider';

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

        foreach ($extras as $extraKey => $extraClass) {
            if (
                ! array_key_exists($extraClass, $options)
                || ! array_key_exists('enabled', $options[$extraClass])
                || $options[$extraClass]['enabled'] === false
            ) {
                $this->$extraKey = new $extraClass(false);
                continue;
            }

            if (
                ! array_key_exists('processor', $options[$extraClass])
                || ! is_array($options[$extraClass]['processor'])
                || ! array_key_exists('class', $options[$extraClass]['processor'])
                || ! is_string($options[$extraClass]['processor']['class'])
                || ! class_exists($options[$extraClass]['processor']['class'])
            ) {
                $this->$extraKey = new $extraClass(true);
                continue;
            }

            $sensitiveParameters = [];
            if (
                array_key_exists('sensitiveParameters', $options[$extraClass]['processor'])
                && is_array($options[$extraClass]['processor']['sensitiveParameters'])
                && count($options[$extraClass]['processor']['sensitiveParameters']) > 0
            ) {
                $sensitiveParameters = $options[$extraClass]['processor']['sensitiveParameters'];
                $sensitiveParameters = array_map('strtolower', $sensitiveParameters);
                $sensitiveParameters = array_flip($sensitiveParameters);
            }

            $replacementStrategy = ReplacementStrategy::Full;
            if (
                array_key_exists('replacementStrategy', $options[$extraClass]['processor'])
                && $options[$extraClass]['processor']['replacementStrategy'] instanceof ReplacementStrategy
            ) {
                $replacementStrategy = $options[$extraClass]['processor']['replacementStrategy'];
            }

            $processor = new $options[$extraClass]['processor']['class']($sensitiveParameters, $replacementStrategy);
            assert($processor instanceof ProcessorInterface);

            $this->$extraKey = new $extraClass(true, $processor);
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
