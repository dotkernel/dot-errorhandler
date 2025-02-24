<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Extra;

use Dot\ErrorHandler\Extra\ExtraProvider;
use Dot\ErrorHandler\Extra\Processor\CookieProcessor;
use Dot\ErrorHandler\Extra\Processor\HeaderProcessor;
use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;
use Dot\ErrorHandler\Extra\Processor\RequestProcessor;
use Dot\ErrorHandler\Extra\Processor\ServerProcessor;
use Dot\ErrorHandler\Extra\Processor\SessionProcessor;
use Dot\ErrorHandler\Extra\Processor\TraceProcessor;
use Dot\ErrorHandler\Extra\Provider\CookieProvider;
use Dot\ErrorHandler\Extra\Provider\HeaderProvider;
use Dot\ErrorHandler\Extra\Provider\RequestProvider;
use Dot\ErrorHandler\Extra\Provider\ServerProvider;
use Dot\ErrorHandler\Extra\Provider\SessionProvider;
use Dot\ErrorHandler\Extra\Provider\TraceProvider;
use Dot\ErrorHandler\Extra\ReplacementStrategy;
use Laminas\Stdlib\ArrayObject;
use PHPUnit\Framework\TestCase;

use function array_flip;

class ExtraProviderTest extends TestCase
{
    public function testWillInstantiateWithoutOptions(): void
    {
        $extraProvider = new ExtraProvider();

        $this->assertFalse($extraProvider->getCookie()->isEnabled());
        $this->assertNull($extraProvider->getCookie()->getProcessor());

        $this->assertFalse($extraProvider->getHeader()->isEnabled());
        $this->assertNull($extraProvider->getHeader()->getProcessor());

        $this->assertFalse($extraProvider->getRequest()->isEnabled());
        $this->assertNull($extraProvider->getRequest()->getProcessor());

        $this->assertFalse($extraProvider->getServer()->isEnabled());
        $this->assertNull($extraProvider->getServer()->getProcessor());

        $this->assertFalse($extraProvider->getSession()->isEnabled());
        $this->assertNull($extraProvider->getSession()->getProcessor());

        $this->assertFalse($extraProvider->getTrace()->isEnabled());
        $this->assertNull($extraProvider->getTrace()->getProcessor());
    }

    public function testWillInstantiateWithInvalidOptions(): void
    {
        $extraProvider = new ExtraProvider(['test' => 'test']);

        $this->assertFalse($extraProvider->getCookie()->isEnabled());
        $this->assertNull($extraProvider->getCookie()->getProcessor());

        $this->assertFalse($extraProvider->getHeader()->isEnabled());
        $this->assertNull($extraProvider->getHeader()->getProcessor());

        $this->assertFalse($extraProvider->getRequest()->isEnabled());
        $this->assertNull($extraProvider->getRequest()->getProcessor());

        $this->assertFalse($extraProvider->getServer()->isEnabled());
        $this->assertNull($extraProvider->getServer()->getProcessor());

        $this->assertFalse($extraProvider->getSession()->isEnabled());
        $this->assertNull($extraProvider->getSession()->getProcessor());

        $this->assertFalse($extraProvider->getTrace()->isEnabled());
        $this->assertNull($extraProvider->getTrace()->getProcessor());
    }

    public function testWillNotEnableProvidersWhenEmptyProviderOptions(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class  => [],
            HeaderProvider::class  => [],
            RequestProvider::class => [],
            ServerProvider::class  => [],
            SessionProvider::class => [],
            TraceProvider::class   => [],
        ]);

        $this->assertFalse($extraProvider->getCookie()->isEnabled());
        $this->assertNull($extraProvider->getCookie()->getProcessor());

        $this->assertFalse($extraProvider->getHeader()->isEnabled());
        $this->assertNull($extraProvider->getHeader()->getProcessor());

        $this->assertFalse($extraProvider->getRequest()->isEnabled());
        $this->assertNull($extraProvider->getRequest()->getProcessor());

        $this->assertFalse($extraProvider->getServer()->isEnabled());
        $this->assertNull($extraProvider->getServer()->getProcessor());

        $this->assertFalse($extraProvider->getSession()->isEnabled());
        $this->assertNull($extraProvider->getSession()->getProcessor());

        $this->assertFalse($extraProvider->getTrace()->isEnabled());
        $this->assertNull($extraProvider->getTrace()->getProcessor());
    }

    public function testWillEnableProvidersWhenEnabledIsTrueInProviderOptions(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class  => ['enabled' => true],
            HeaderProvider::class  => ['enabled' => true],
            RequestProvider::class => ['enabled' => true],
            ServerProvider::class  => ['enabled' => true],
            SessionProvider::class => ['enabled' => true],
            TraceProvider::class   => ['enabled' => true],
        ]);

        $this->assertTrue($extraProvider->getCookie()->isEnabled());
        $this->assertNull($extraProvider->getCookie()->getProcessor());

        $this->assertTrue($extraProvider->getHeader()->isEnabled());
        $this->assertNull($extraProvider->getHeader()->getProcessor());

        $this->assertTrue($extraProvider->getRequest()->isEnabled());
        $this->assertNull($extraProvider->getRequest()->getProcessor());

        $this->assertTrue($extraProvider->getServer()->isEnabled());
        $this->assertNull($extraProvider->getServer()->getProcessor());

        $this->assertTrue($extraProvider->getSession()->isEnabled());
        $this->assertNull($extraProvider->getSession()->getProcessor());

        $this->assertTrue($extraProvider->getTrace()->isEnabled());
        $this->assertNull($extraProvider->getTrace()->getProcessor());
    }

    public function testProvidersWillNotHaveProcessorWhenEmptyProcessorOptions(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class  => ['enabled' => true, 'processor' => []],
            HeaderProvider::class  => ['enabled' => true, 'processor' => []],
            RequestProvider::class => ['enabled' => true, 'processor' => []],
            ServerProvider::class  => ['enabled' => true, 'processor' => []],
            SessionProvider::class => ['enabled' => true, 'processor' => []],
            TraceProvider::class   => ['enabled' => true, 'processor' => []],
        ]);

        $this->assertTrue($extraProvider->getCookie()->isEnabled());
        $this->assertNull($extraProvider->getCookie()->getProcessor());

        $this->assertTrue($extraProvider->getHeader()->isEnabled());
        $this->assertNull($extraProvider->getHeader()->getProcessor());

        $this->assertTrue($extraProvider->getRequest()->isEnabled());
        $this->assertNull($extraProvider->getRequest()->getProcessor());

        $this->assertTrue($extraProvider->getServer()->isEnabled());
        $this->assertNull($extraProvider->getServer()->getProcessor());

        $this->assertTrue($extraProvider->getSession()->isEnabled());
        $this->assertNull($extraProvider->getSession()->getProcessor());

        $this->assertTrue($extraProvider->getTrace()->isEnabled());
        $this->assertNull($extraProvider->getTrace()->getProcessor());
    }

    public function testProvidersWillNotHaveProcessorWhenInvalidProcessorOptions(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class  => ['enabled' => true, 'processor' => ['class' => 'test']],
            HeaderProvider::class  => ['enabled' => true, 'processor' => ['class' => 'test']],
            RequestProvider::class => ['enabled' => true, 'processor' => ['class' => 'test']],
            ServerProvider::class  => ['enabled' => true, 'processor' => ['class' => 'test']],
            SessionProvider::class => ['enabled' => true, 'processor' => ['class' => 'test']],
            TraceProvider::class   => ['enabled' => true, 'processor' => ['class' => 'test']],
        ]);

        $this->assertTrue($extraProvider->getCookie()->isEnabled());
        $this->assertNull($extraProvider->getCookie()->getProcessor());

        $this->assertTrue($extraProvider->getHeader()->isEnabled());
        $this->assertNull($extraProvider->getHeader()->getProcessor());

        $this->assertTrue($extraProvider->getRequest()->isEnabled());
        $this->assertNull($extraProvider->getRequest()->getProcessor());

        $this->assertTrue($extraProvider->getServer()->isEnabled());
        $this->assertNull($extraProvider->getServer()->getProcessor());

        $this->assertTrue($extraProvider->getSession()->isEnabled());
        $this->assertNull($extraProvider->getSession()->getProcessor());

        $this->assertTrue($extraProvider->getTrace()->isEnabled());
        $this->assertNull($extraProvider->getTrace()->getProcessor());
    }

    public function testProvidersWillHaveProcessorWhenValidProcessorOptions(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class  => ['enabled' => true, 'processor' => ['class' => CookieProcessor::class]],
            HeaderProvider::class  => ['enabled' => true, 'processor' => ['class' => HeaderProcessor::class]],
            RequestProvider::class => ['enabled' => true, 'processor' => ['class' => RequestProcessor::class]],
            ServerProvider::class  => ['enabled' => true, 'processor' => ['class' => ServerProcessor::class]],
            SessionProvider::class => ['enabled' => true, 'processor' => ['class' => SessionProcessor::class]],
            TraceProvider::class   => ['enabled' => true, 'processor' => ['class' => TraceProcessor::class]],
        ]);

        $this->assertTrue($extraProvider->getCookie()->isEnabled());
        $processor = $extraProvider->getCookie()->getProcessor();
        $this->assertInstanceOf(CookieProcessor::class, $processor);
        $this->assertCount(0, $processor->getSensitiveParameters());
        $this->assertEquals(ReplacementStrategy::Full, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getHeader()->isEnabled());
        $processor = $extraProvider->getHeader()->getProcessor();
        $this->assertInstanceOf(HeaderProcessor::class, $processor);
        $this->assertCount(0, $processor->getSensitiveParameters());
        $this->assertEquals(ReplacementStrategy::Full, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getRequest()->isEnabled());
        $processor = $extraProvider->getRequest()->getProcessor();
        $this->assertInstanceOf(RequestProcessor::class, $processor);
        $this->assertCount(0, $processor->getSensitiveParameters());
        $this->assertEquals(ReplacementStrategy::Full, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getServer()->isEnabled());
        $processor = $extraProvider->getServer()->getProcessor();
        $this->assertInstanceOf(ServerProcessor::class, $processor);
        $this->assertCount(0, $processor->getSensitiveParameters());
        $this->assertEquals(ReplacementStrategy::Full, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getSession()->isEnabled());
        $processor = $extraProvider->getSession()->getProcessor();
        $this->assertInstanceOf(SessionProcessor::class, $processor);
        $this->assertCount(0, $processor->getSensitiveParameters());
        $this->assertEquals(ReplacementStrategy::Full, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getTrace()->isEnabled());
        $processor = $extraProvider->getTrace()->getProcessor();
        $this->assertInstanceOf(TraceProcessor::class, $processor);
        $this->assertCount(0, $processor->getSensitiveParameters());
        $this->assertEquals(ReplacementStrategy::Full, $processor->getReplacementStrategy());
    }

    public function testProvidersWillHaveProcessorWhenAllValidProcessorOptions(): void
    {
        $sensitiveParameters = ['test'];

        $extraProvider = new ExtraProvider([
            CookieProvider::class  => [
                'enabled'   => true,
                'processor' => [
                    'class'               => CookieProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => $sensitiveParameters,
                ],
            ],
            HeaderProvider::class  => [
                'enabled'   => true,
                'processor' => [
                    'class'               => HeaderProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => $sensitiveParameters,
                ],
            ],
            RequestProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => RequestProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => $sensitiveParameters,
                ],
            ],
            ServerProvider::class  => [
                'enabled'   => true,
                'processor' => [
                    'class'               => ServerProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => $sensitiveParameters,
                ],
            ],
            SessionProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => SessionProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => $sensitiveParameters,
                ],
            ],
            TraceProvider::class   => [
                'enabled'   => true,
                'processor' => [
                    'class'               => TraceProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => $sensitiveParameters,
                ],
            ],
        ]);

        $this->assertTrue($extraProvider->getCookie()->isEnabled());
        $processor = $extraProvider->getCookie()->getProcessor();
        $this->assertInstanceOf(CookieProcessor::class, $processor);
        $this->assertCount(1, $processor->getSensitiveParameters());
        $this->assertEquals($sensitiveParameters, array_flip($processor->getSensitiveParameters()));
        $this->assertEquals(ReplacementStrategy::Partial, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getHeader()->isEnabled());
        $processor = $extraProvider->getHeader()->getProcessor();
        $this->assertInstanceOf(HeaderProcessor::class, $processor);
        $this->assertCount(1, $processor->getSensitiveParameters());
        $this->assertEquals($sensitiveParameters, array_flip($processor->getSensitiveParameters()));
        $this->assertEquals(ReplacementStrategy::Partial, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getRequest()->isEnabled());
        $processor = $extraProvider->getRequest()->getProcessor();
        $this->assertInstanceOf(RequestProcessor::class, $processor);
        $this->assertCount(1, $processor->getSensitiveParameters());
        $this->assertEquals($sensitiveParameters, array_flip($processor->getSensitiveParameters()));
        $this->assertEquals(ReplacementStrategy::Partial, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getServer()->isEnabled());
        $processor = $extraProvider->getServer()->getProcessor();
        $this->assertInstanceOf(ServerProcessor::class, $processor);
        $this->assertCount(1, $processor->getSensitiveParameters());
        $this->assertEquals($sensitiveParameters, array_flip($processor->getSensitiveParameters()));
        $this->assertEquals(ReplacementStrategy::Partial, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getSession()->isEnabled());
        $processor = $extraProvider->getSession()->getProcessor();
        $this->assertInstanceOf(SessionProcessor::class, $processor);
        $this->assertCount(1, $processor->getSensitiveParameters());
        $this->assertEquals($sensitiveParameters, array_flip($processor->getSensitiveParameters()));
        $this->assertEquals(ReplacementStrategy::Partial, $processor->getReplacementStrategy());

        $this->assertTrue($extraProvider->getTrace()->isEnabled());
        $processor = $extraProvider->getTrace()->getProcessor();
        $this->assertInstanceOf(TraceProcessor::class, $processor);
        $this->assertCount(1, $processor->getSensitiveParameters());
        $this->assertEquals($sensitiveParameters, array_flip($processor->getSensitiveParameters()));
        $this->assertEquals(ReplacementStrategy::Partial, $processor->getReplacementStrategy());
    }

    public function testWillProvideUnmodifiedCookieDataWhenNoProcessor(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class => [
                'enabled' => true,
            ],
        ]);

        $input  = ['test' => 'test'];
        $output = $extraProvider->getCookie()->provide($input);
        $this->assertSame($input, $output);
    }

    public function testWillProvideModifiedCookieDataWhenProcessorSetToPartialReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => CookieProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $output = $extraProvider->getCookie()->provide(['first' => 'First', 'second' => 'Second', 'third' => 'Third']);
        $this->assertSame(['first' => 'Fir**', 'second' => 'Sec***', 'third' => 'Thi**'], $output);
    }

    public function testWillProvideModifiedCookieDataWhenProcessorSetToFullReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => CookieProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $output = $extraProvider->getCookie()->provide(['first' => 'First', 'second' => 'Second', 'third' => 'Third']);
        $this->assertSame(['first' => '*****', 'second' => '******', 'third' => '*****'], $output);
    }

    public function testWillProvideModifiedCookieDataWhenProcessorSetToPartialReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => CookieProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => ['second'],
                ],
            ],
        ]);

        $output = $extraProvider->getCookie()->provide(['first' => 'First', 'second' => 'Second', 'third' => 'Third']);
        $this->assertSame(['first' => 'First', 'second' => 'Sec***', 'third' => 'Third'], $output);
    }

    public function testWillProvideModifiedCookieDataWhenProcessorSetToFullReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            CookieProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => CookieProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => ['second'],
                ],
            ],
        ]);

        $output = $extraProvider->getCookie()->provide(['first' => 'First', 'second' => 'Second', 'third' => 'Third']);
        $this->assertSame(['first' => 'First', 'second' => '******', 'third' => 'Third'], $output);
    }

    public function testWillProvideUnmodifiedHeaderDataWhenNoProcessor(): void
    {
        $extraProvider = new ExtraProvider([
            HeaderProvider::class => [
                'enabled' => true,
            ],
        ]);

        $input  = ['cookie' => 'Test-data'];
        $output = $extraProvider->getHeader()->provide($input);
        $this->assertSame($input, $output);
    }

    public function testWillProvideModifiedHeaderDataWhenProcessorSetToPartialReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            HeaderProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => HeaderProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'cookie' => 'rememberMe=s0me-v3ry-l0ng-h4sh',
        ]);
        $this->assertSame(['test' => 'Te**', 'cookie' => 'rememberMe=s0me-v3ry-*********'], $output);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'cookie' => ['rememberMe=s0me-v3ry-l0ng-h4sh'],
        ]);
        $this->assertSame(['test' => 'Te**', 'cookie' => 'rememberMe=s0me-v3ry-*********'], $output);
    }

    public function testWillProvideModifiedHeaderDataWhenProcessorSetToFullReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            HeaderProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => HeaderProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'cookie' => 'rememberMe=s0me-v3ry-l0ng-h4sh',
        ]);
        $this->assertSame(['test' => '****', 'cookie' => 'rememberMe=*******************'], $output);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'cookie' => ['rememberMe=s0me-v3ry-l0ng-h4sh'],
        ]);
        $this->assertSame(['test' => '****', 'cookie' => 'rememberMe=*******************'], $output);
    }

    public function testWillProvideModifiedHeaderDataWhenProcessorSetToPartialReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            HeaderProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => HeaderProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => ['test'],
                ],
            ],
        ]);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'data'   => 'Data',
            'cookie' => 'rememberMe=s0me-v3ry-l0ng-h4sh',
        ]);
        $this->assertSame(['test' => 'Te**', 'data' => 'Data', 'cookie' => 'rememberMe=s0me-v3ry-*********'], $output);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'data'   => 'Data',
            'cookie' => ['rememberMe=s0me-v3ry-l0ng-h4sh'],
        ]);
        $this->assertSame(['test' => 'Te**', 'data' => 'Data', 'cookie' => 'rememberMe=s0me-v3ry-*********'], $output);
    }

    public function testWillProvideModifiedHeaderDataWhenProcessorSetToFullReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            HeaderProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => HeaderProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => ['test'],
                ],
            ],
        ]);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'data'   => 'Data',
            'cookie' => 'rememberMe=s0me-v3ry-l0ng-h4sh',
        ]);
        $this->assertSame(['test' => '****', 'data' => 'Data', 'cookie' => 'rememberMe=*******************'], $output);

        $output = $extraProvider->getHeader()->provide([
            'test'   => 'Test',
            'data'   => 'Data',
            'cookie' => ['rememberMe=s0me-v3ry-l0ng-h4sh'],
        ]);
        $this->assertSame(['test' => '****', 'data' => 'Data', 'cookie' => 'rememberMe=*******************'], $output);
    }

    public function testWillProvideUnmodifiedRequestDataWhenNoProcessor(): void
    {
        $extraProvider = new ExtraProvider([
            RequestProvider::class => [
                'enabled' => true,
            ],
        ]);

        $input  = ['foo' => 'bar', 'bar' => ['baz' => 'foo']];
        $output = $extraProvider->getRequest()->provide($input);
        $this->assertSame($input, $output);
    }

    public function testWillProvideModifiedRequestDataWhenProcessorSetToPartialReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            RequestProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => RequestProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $input  = ['foo' => 'bar', 'bar' => ['baz' => 'foo']];
        $output = $extraProvider->getRequest()->provide($input);
        $this->assertSame(['foo' => 'ba*', 'bar' => ['baz' => 'fo*']], $output);
    }

    public function testWillProvideModifiedRequestDataWhenProcessorSetToFullReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            RequestProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => RequestProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $input  = ['foo' => 'bar', 'bar' => ['baz' => 'foo']];
        $output = $extraProvider->getRequest()->provide($input);
        $this->assertSame(['foo' => '***', 'bar' => ['baz' => '***']], $output);
    }

    public function testWillProvideModifiedRequestDataWhenProcessorSetToPartialReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            RequestProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => RequestProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => ['ba'],
                ],
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => ['bar' => 'Bar'], 'baz' => 'Baz'];
        $output = $extraProvider->getRequest()->provide($input);
        $this->assertSame(['foo' => 'Foo', 'bar' => ['bar' => 'Ba*'], 'baz' => 'Ba*'], $output);
    }

    public function testWillProvideModifiedRequestDataWhenProcessorSetToFullReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            RequestProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => RequestProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => ['ba'],
                ],
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => ['bar' => 'Bar'], 'baz' => 'Baz'];
        $output = $extraProvider->getRequest()->provide($input);
        $this->assertSame(['foo' => 'Foo', 'bar' => ['bar' => '***'], 'baz' => '***'], $output);
    }

    public function testWillProvideUnmodifiedServerDataWhenNoProcessor(): void
    {
        $extraProvider = new ExtraProvider([
            ServerProvider::class => [
                'enabled' => true,
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=Bar; baz=Baz'];
        $output = $extraProvider->getServer()->provide($input);
        $this->assertSame($input, $output);
    }

    public function testWillProvideModifiedServerDataWhenProcessorSetToPartialReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            ServerProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => ServerProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=Bar; baz=Baz'];
        $output = $extraProvider->getServer()->provide($input);
        $this->assertSame(
            ['foo' => 'Fo*', 'bar' => 'Ba*', 'baz' => 'Ba*', 'HTTP_COOKIE' => 'foo=Fo*; bar=Ba*; baz=Ba*'],
            $output
        );
    }

    public function testWillProvideModifiedServerDataWhenProcessorSetToFullReplaceAllKeys(): void
    {
        $extraProvider = new ExtraProvider([
            ServerProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => ServerProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => [ProcessorInterface::ALL],
                ],
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=Bar; baz=Baz'];
        $output = $extraProvider->getServer()->provide($input);
        $this->assertSame(
            ['foo' => '***', 'bar' => '***', 'baz' => '***', 'HTTP_COOKIE' => 'foo=***; bar=***; baz=***'],
            $output
        );
    }

    public function testWillProvideModifiedServerDataWhenProcessorSetToPartialReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            ServerProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => ServerProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Partial,
                    'sensitiveParameters' => ['bar'],
                ],
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=Bar; baz=Baz'];
        $output = $extraProvider->getServer()->provide($input);
        $this->assertSame(
            ['foo' => 'Foo', 'bar' => 'Ba*', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=Ba*; baz=Baz'],
            $output
        );
    }

    public function testWillProvideModifiedServerDataWhenProcessorSetToFullReplaceSpecificKeys(): void
    {
        $extraProvider = new ExtraProvider([
            ServerProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class'               => ServerProcessor::class,
                    'replacementStrategy' => ReplacementStrategy::Full,
                    'sensitiveParameters' => ['bar'],
                ],
            ],
        ]);

        $input  = ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=Bar; baz=Baz'];
        $output = $extraProvider->getServer()->provide($input);
        $this->assertSame(
            ['foo' => 'Foo', 'bar' => '***', 'baz' => 'Baz', 'HTTP_COOKIE' => 'foo=Foo; bar=***; baz=Baz'],
            $output
        );
    }

    public function testWillProvideUnmodifiedSessionDataWhenNoProcessor(): void
    {
        $extraProvider = new ExtraProvider([
            SessionProvider::class => [
                'enabled' => true,
            ],
        ]);

        $input  = ['foo' => ['foo' => 'Foo'], 'bar' => new ArrayObject(['bar' => 'Bar'])];
        $output = $extraProvider->getSession()->provide($input);
        $this->assertSame($input, $output);
    }

    public function testWillProvideModifiedSessionDataWhenProcessorIsSpecified(): void
    {
        $extraProvider = new ExtraProvider([
            SessionProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class' => SessionProcessor::class,
                ],
            ],
        ]);

        $input  = ['foo' => ['foo' => 'Foo'], 'bar' => new ArrayObject(['bar' => 'Bar'])];
        $output = $extraProvider->getSession()->provide($input);
        $this->assertSame(['foo' => ['foo' => 'Foo'], 'bar' => ['bar' => 'Bar']], $output);
    }

    public function testWillProvideUnmodifiedTraceDataWhenNoProcessor(): void
    {
        $extraProvider = new ExtraProvider([
            TraceProvider::class => [
                'enabled' => true,
            ],
        ]);

        $input  = [
            ['file' => '/path/to/some/class.php', 'line' => 1, 'function' => 'foo', 'class' => 'Foo', 'type' => '->'],
            ['file' => '/path/to/index.php', 'line' => 1, 'function' => 'bar'],
        ];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame($input, $output);
    }

    public function testWillProvideUnmodifiedTraceDataWhenProcessorIsSpecified(): void
    {
        $extraProvider = new ExtraProvider([
            TraceProvider::class => [
                'enabled'   => true,
                'processor' => [
                    'class' => TraceProcessor::class,
                ],
            ],
        ]);

        $input  = [
            ['file' => '/path/to/some/class.php', 'line' => 8, 'function' => 'foo', 'class' => 'Foo', 'type' => '->'],
            ['file' => '/path/to/index.php', 'line' => 8, 'function' => 'bar'],
        ];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame(['Foo->foo:8', '/path/to/index.php->bar:8'], $output);

        $input  = [
            ['file' => '/path/to/some/class.php', 'line' => 8, 'function' => 'foo', 'class' => 'Foo'],
            ['file' => '/path/to/index.php', 'line' => 8, 'function' => 'bar'],
        ];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame(['Foo->foo:8', '/path/to/index.php->bar:8'], $output);

        $input  = [
            ['file' => '/path/to/some/class.php', 'line' => 8, 'class' => 'Foo'],
            ['file' => '/path/to/index.php', 'line' => 8],
        ];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame(['Foo->unknown:8', '/path/to/index.php->unknown:8'], $output);

        $input  = [
            ['file' => '/path/to/some/class.php', 'line' => 8],
            ['file' => '/path/to/index.php', 'line' => 8],
        ];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame(['/path/to/some/class.php->unknown:8', '/path/to/index.php->unknown:8'], $output);

        $input  = [
            ['file' => '/path/to/some/class.php'],
            ['file' => '/path/to/index.php'],
        ];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame(['/path/to/some/class.php->unknown:0', '/path/to/index.php->unknown:0'], $output);

        $input  = [[], []];
        $output = $extraProvider->getTrace()->provide($input);
        $this->assertSame(['unknown->unknown:0', 'unknown->unknown:0'], $output);
    }
}
