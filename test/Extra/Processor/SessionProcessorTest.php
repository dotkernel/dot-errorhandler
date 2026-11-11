<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\Processor\SessionProcessor;
use Laminas\Stdlib\ArrayObject;
use PHPUnit\Framework\TestCase;

class SessionProcessorTest extends TestCase
{
    /**
     * F6: plain PHP sessions store scalars at the top level.
     */
    public function testWillPassScalarSessionValuesThrough(): void
    {
        $processor = new SessionProcessor();

        $this->assertSame(
            ['user_id' => 5, 'flag' => true, 'container' => ['a' => 1]],
            $processor->process(['user_id' => 5, 'flag' => true, 'container' => new ArrayObject(['a' => 1])])
        );
    }
}
