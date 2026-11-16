<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\Extra\ExtraProvider;
use Dot\ErrorHandler\Extra\Provider\HeaderProvider;
use PHPUnit\Framework\TestCase;

/**
 * Checks the shipped config/error-handling.global.php.dist.
 */
class DistConfigTest extends TestCase
{
    /**
     * F7: enabling the shipped header provider must not log credentials in clear.
     */
    public function testHeaderProviderMasksCredentialsByDefault(): void
    {
        /** @var array{dot-errorhandler: array{extraProvider: array<class-string, array<string, mixed>>}} $config */
        $config  = require __DIR__ . '/../config/error-handling.global.php.dist';
        $options = $config['dot-errorhandler'][ExtraProvider::CONFIG_KEY];

        $options[HeaderProvider::class]['enabled'] = true;

        $output = (new ExtraProvider($options))->getHeader()->provide([
            'Authorization'       => ['Bearer abc'],
            'Proxy-Authorization' => ['Basic xyz'],
            'Accept'              => ['*/*'],
        ]);

        $this->assertSame(
            ['Authorization' => '**********', 'Proxy-Authorization' => '*********', 'Accept' => '*/*'],
            $output
        );
    }
}
