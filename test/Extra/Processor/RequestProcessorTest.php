<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;
use Dot\ErrorHandler\Extra\Processor\RequestProcessor;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_flip;

class RequestProcessorTest extends TestCase
{
    /**
     * F3: a form field such as tags[] produces a list with integer keys.
     */
    public function testWillProcessListArrays(): void
    {
        $processor = new RequestProcessor(array_flip(['password']));

        $this->assertSame(
            ['tags' => ['a', 'b'], 'password' => '******'],
            $processor->process(['tags' => ['a', 'b'], 'password' => 'secret'])
        );
    }

    /**
     * F3: integer keys at the top level.
     */
    public function testWillProcessIntegerKeys(): void
    {
        $processor = new RequestProcessor(array_flip(['password']));

        $this->assertSame(
            [0 => 'a', 'password' => '******'],
            $processor->process([0 => 'a', 'password' => 'secret'])
        );
    }

    /**
     * F3: array_flip() turns the numeric sensitive name '123' into the integer key 123.
     */
    public function testWillMatchNumericSensitiveParameterNames(): void
    {
        $processor = new RequestProcessor(array_flip(['123']));

        $this->assertSame(
            ['code123' => '***', 'other' => 'xyz'],
            $processor->process(['code123' => 'abc', 'other' => 'xyz'])
        );
    }

    /**
     * F3: non-string values are masked from their string form instead of crashing.
     */
    public function testWillMaskNonStringValues(): void
    {
        $processor = new RequestProcessor(array_flip([ProcessorInterface::ALL]));

        $this->assertSame(
            ['object' => '********', 'null' => '', 'int' => '**', 'float' => '***', 'bool' => '*'],
            $processor->process([
                'object' => new stdClass(),
                'null'   => null,
                'int'    => 42,
                'float'  => 1.5,
                'bool'   => true,
            ])
        );
    }
}
