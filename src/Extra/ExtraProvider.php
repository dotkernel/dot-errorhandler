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
use function is_a;
use function is_array;
use function is_int;
use function is_string;
use function strtolower;

class ExtraProvider
{
    public const CONFIG_KEY = 'extraProvider';

    private CookieProvider $cookie;
    private HeaderProvider $header;
    private RequestProvider $request;
    private ServerProvider $server;
    private SessionProvider $session;
    private TraceProvider $trace;

    /**
     * @param array<array-key, mixed> $options provider class => provider options; validated here
     */
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
            $providerOptions = $options[$extraClass] ?? null;
            if (! is_array($providerOptions) || ($providerOptions['enabled'] ?? false) !== true) {
                $this->$extraKey = new $extraClass(false);
                continue;
            }

            $processorOptions = $providerOptions['processor'] ?? null;
            $processorClass   = is_array($processorOptions) ? ($processorOptions['class'] ?? null) : null;
            if (
                ! is_array($processorOptions)
                || ! is_string($processorClass)
                || ! is_a($processorClass, ProcessorInterface::class, true)
            ) {
                $this->$extraKey = new $extraClass(true);
                continue;
            }

            $replacementStrategy = $processorOptions['replacementStrategy'] ?? null;
            if (! $replacementStrategy instanceof ReplacementStrategy) {
                $replacementStrategy = ReplacementStrategy::Full;
            }

            $processor = new $processorClass(
                $this->normalizeSensitiveParameters($processorOptions['sensitiveParameters'] ?? null),
                $replacementStrategy
            );

            $this->$extraKey = new $extraClass(true, $processor);
        }
    }

    /**
     * Keeps string and integer names only, lower-cased, as keys.
     *
     * @return array<array-key, int>
     */
    private function normalizeSensitiveParameters(mixed $sensitiveParameters): array
    {
        if (! is_array($sensitiveParameters)) {
            return [];
        }

        $names = [];
        foreach ($sensitiveParameters as $name) {
            if (is_string($name) || is_int($name)) {
                $names[] = strtolower((string) $name);
            }
        }

        return array_flip($names);
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
