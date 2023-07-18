<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\ConfigProvider;
use Dot\ErrorHandler\ErrorHandler;
use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    private array $config;

    protected function setUp(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHaveAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertArrayHasKey(ErrorHandlerInterface::class, $this->config['dependencies']['aliases']);

        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(LogErrorHandler::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(ErrorHandler::class, $this->config['dependencies']['factories']);
    }
}
