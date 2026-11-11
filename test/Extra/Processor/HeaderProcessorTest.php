<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\Processor\HeaderProcessor;
use PHPUnit\Framework\TestCase;

use function array_flip;

class HeaderProcessorTest extends TestCase
{
    /**
     * F7: header names are case-insensitive; withHeader('Cookie', ...) keeps the capital C.
     */
    public function testWillMaskCookieHeaderRegardlessOfCase(): void
    {
        $processor = new HeaderProcessor();

        $this->assertSame(
            ['Cookie' => 'sid=******', 'cookie' => 'sid=******'],
            $processor->process(['Cookie' => ['sid=secret'], 'cookie' => ['sid=secret']])
        );
    }

    /**
     * F4: a numeric header name arrives as an integer key.
     */
    public function testWillProcessNumericHeaderNames(): void
    {
        $processor = new HeaderProcessor(array_flip(['authorization']));

        $this->assertSame(
            [123 => 'value', 'Authorization' => '**********'],
            $processor->process(['123' => ['value'], 'Authorization' => ['Bearer abc']])
        );
    }
}
