<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\Processor\CookieProcessor;
use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;
use Dot\ErrorHandler\Extra\ReplacementStrategy;
use PHPUnit\Framework\TestCase;

use function array_flip;
use function implode;
use function mb_check_encoding;

class CookieProcessorTest extends TestCase
{
    /**
     * F4: a client controls cookie names; '123' arrives as the integer key 123.
     */
    public function testWillProcessNumericCookieNames(): void
    {
        $processor = new CookieProcessor(array_flip(['sid']));

        $this->assertSame(
            [123 => 'x', 'sid' => '***'],
            $processor->process(['123' => 'x', 'sid' => 'abc'])
        );
    }

    /**
     * F8: partial masking must not split a multibyte character.
     */
    public function testPartialMaskingKeepsValidUtf8(): void
    {
        $processor = new CookieProcessor(array_flip([ProcessorInterface::ALL]), ReplacementStrategy::Partial);

        $output = $processor->process(['city' => 'Brașov', 'name' => 'Zoë']);

        $this->assertSame(['city' => 'Bra***', 'name' => 'Zo*'], $output);
        $this->assertTrue(mb_check_encoding(implode('', $output), 'UTF-8'));
    }

    /**
     * F8: full masking counts characters, not bytes.
     */
    public function testFullMaskingCountsCharacters(): void
    {
        $processor = new CookieProcessor(array_flip([ProcessorInterface::ALL]), ReplacementStrategy::Full);

        $this->assertSame(['city' => '******'], $processor->process(['city' => 'Brașov']));
    }
}
