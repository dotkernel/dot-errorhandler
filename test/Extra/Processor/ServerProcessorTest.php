<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;
use Dot\ErrorHandler\Extra\Processor\ServerProcessor;
use PHPUnit\Framework\TestCase;

use function array_flip;

class ServerProcessorTest extends TestCase
{
    /**
     * F4: a cookie named '1' becomes the integer key 1 once HTTP_COOKIE is split.
     */
    public function testWillProcessNumericCookieNamesInHttpCookie(): void
    {
        $processor = new ServerProcessor();

        $this->assertSame(
            ['HTTP_COOKIE' => '1=a; b=c'],
            $processor->process(['HTTP_COOKIE' => '1=a; b=c'])
        );
    }

    /**
     * F5: argv is an array whenever register_argc_argv is on (CLI, RoadRunner, Swoole).
     */
    public function testWillMaskArrayValuesElementByElement(): void
    {
        $processor = new ServerProcessor(array_flip([ProcessorInterface::ALL]));

        $this->assertSame(
            ['argv' => ['***', '****'], 'argc' => '*'],
            $processor->process(['argv' => ['run', '--do'], 'argc' => 2])
        );
    }

    /**
     * F5: array values that are not sensitive are left unchanged.
     */
    public function testWillKeepArrayValuesThatAreNotSensitive(): void
    {
        $processor = new ServerProcessor(array_flip(['server_admin']));

        $this->assertSame(
            ['argv' => ['run', '--do']],
            $processor->process(['argv' => ['run', '--do']])
        );
    }

    /**
     * F7: cookie pairs separated by ';' without a space must still be split.
     */
    public function testWillSplitHttpCookieWithoutSpaces(): void
    {
        $processor = new ServerProcessor(array_flip(['b']));

        $this->assertSame(
            ['HTTP_COOKIE' => 'a=1; b=*******'],
            $processor->process(['HTTP_COOKIE' => 'a=1;b=secret2'])
        );
    }
}
