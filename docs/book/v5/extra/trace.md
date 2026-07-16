# Log trace data

Looking at `dot-errorhandler`'s config file, the array found at `TraceProvider::class` allows you to configure the behavior of this provider:

- **enabled**: enabled/disable this provider
- **processor**: an array configuring the data processor to be used by the **TraceProvider**:
    - **class**: data processor class implementing `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`

## Configure provider

By default, **TraceProvider** is enabled.
It can be enabled only by setting **enabled** to **true**.

If **enabled** is set to **true**, your log file will contain an additional field under the `extra` key, called `trace`.
If **enabled** is set to **false**, no additional field is added under the `extra` key.

## Configure processor

From here, we assume that **enabled** is set to **true**.

If **processor** is missing/empty, the processor is ignored the provider will log the raw data available.
If **processor** is specified, but **class** is missing/invalid, the processor is ignored and the provider will log the raw data available.

From here, we assume that **processor**.**class** is valid.

### Replacement strategy

**TraceProcessor** does not use **replacementStrategy**.

> If you create a custom session processor, you may add **replacementStrategy** to the config file; it will get passed to your processor.

### Sensitive parameters

**TraceProcessor** does not use **sensitiveParameters**.

> If you create a custom session processor, you may add **sensitiveParameters** to the config file; it will get passed to your processor.

## Why should I use a processor

The only advantage of using **TraceProcessor** is to reduce the size of the trace data that you log.
In the below example, the trace route contains an array with dozens of items, each with the same structure (except for the last one).

```text
    0 => array:5 [▼
      "file" => "/path/to/some/file.php"
      "line" => 66
      "function" => "someFunction"
      "class" => "Path\To\Some\File"
      "type" => "->"
    ]
    1 => array:5 [▼
      "file" => "/path/to/some/other/file.php"
      "line" => 33
      "function" => "someOtherFunction"
      "class" => "Path\To\Some\Other\File"
      "type" => "->"
    ]
    ...
    58 => array:3 [▼
      "file" => "/path/to/index.php"
      "line" => 36
      "function" => "{closure}"
    ]
```

By using **TraceProcessor**, the array is reduced to:

```text
    0 => "Path\To\Some\File->someFunction:66"
    1 => "Path\To\Some\Other\File->someOtherFunction:33"
    ...
    58 => "/path/to/index.php->{closure}:36"
```

Yet it maintains the relevant information, but in a compact form.
This works because, since using namespaced classes, it's easy to determine file paths just by looking at a FQCN.

## Custom processor

If the existing processor does not offer enough features, you can create a custom processor.
The custom processor must implement `Dot\ErrorHandler\Extra\Processor\ProcessorInterface` or extend `Dot\ErrorHandler\Extra\Processor\AbstractProcessor`, which already implements `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`.
Once the custom processor is ready, you need to configure **TraceProvider** to use it.
For this, open `dot-errorhandler`'s config file and - under **TraceProvider::class** - set **processor**.**class** to the class string of your custom processor:

```php
TraceProvider::class  => [
    'enabled'   => false,
    'processor' => [
        'class'               => CustomTraceProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
        'sensitiveParameters' => [
            ProcessorInterface::ALL,
        ],
    ],
],
```

Using this, trace data will be processed by `CustomTraceProcessor` and logged as provided by this new processor.
