# Log request data

Looking at `dot-errorhandler`'s config file, the array found at `RequestProvider::class` allows you to configure the behavior of this provider:

- **enabled**: enabled/disable this provider
- **processor**: an array configuring the data processor to be used by the **RequestProvider**:
    - **class**: data processor class implementing `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`
    - **replacementStrategy**: whether to replace specific cookie values completely or partially
    - **sensitiveParameters**: an array of cookie names that may contain sensitive information so their value should be masked partially/completely

## Configure provider

By default, **RequestProvider** is disabled.
It can be enabled only by setting **enabled** to **true**.

If **enabled** is set to **true**, your log file will contain an additional field under the `extra` key, called `request`.
If **enabled** is set to **false**, no additional field is added under the `extra` key.

## Configure processor

From here, we assume that **enabled** is set to **true**.

If **processor** is missing/empty, the processor is ignored; the provider will log the raw data available.
If **processor** is specified, but **class** is missing/invalid, the processor is ignored and the provider will log the raw data available.

From here, we assume that **processor**.**class** is valid.

### Replacement strategy

This value should be an instance of `Dot\ErrorHandler\Extra\ReplacementStrategy`.

If **replacementStrategy** is missing/invalid, the default **replacementStrategy** is used, which is `ReplacementStrategy::Full`.
Else, the value used should be one of:

- `ReplacementStrategy::Partial` for half-string replacements (e.g.: "abcdef" becomes "abc***")
- `ReplacementStrategy::Full` for full-string replacements (e.g.: "abcdef" becomes "******")

### Sensitive parameters

If **sensitiveParameters** is missing/empty, the processor is ignored; the provider will log the raw data available.
This is because without a set of **sensitiveParameters**, the processor is unable to determine which key needs to be processed or left untouched.
When specifying the array of **sensitiveParameters**, there are two possibilities:

- use the constant `ProcessorInterface::ALL`, meaning alter all cookie values using the strategy specified by the **replacementStrategy**

```php
'sensitiveParameters' => [
    Dot\ErrorHandler\Extra\Processor\ProcessorInterface::ALL,
],
```

- use exact strings to list the cookies for which the values should be altered using the strategy specified by the **replacementStrategy**

```php
'sensitiveParameters' => [
    'password',
],
```

> **RequestProcessor** uses recursive search to locate all array keys in a multidimensional array.

> **RequestProcessor** uses PARTIAL field name lookups.
> To alter the value of a request field, it is enough to specify only part of the field name.

> The config `sensitiveParameters` is case-insensitive.

## Why should I use a processor

Consider the following request body sent via **$_POST**:

```text
[
    "identity" => "myIdentity",
    "password" => "p4$$w0rd",
    "passwordConfirm" => "p4$$w0rd",
    "details" => [
        "secret" => "s3cr3t",
    ]
]
```

Without a **RequestProcessor**, the plain text passwords would end up saved in the log file:

```text
..."extra":{"file":"/path/to/some/class.php","line":314,"request":{"identity":"myIdentity","password":"p4$$w0rd","passwordConfirm":"p4$$w0rd","details":{"secret":"s3cr3t"}}},...
```

But with a properly configured **RequestProcessor**:

```php
'processor' => [
    'class'               => RequestProcessor::class,
    'replacementStrategy' => ReplacementStrategy::Full,
    'sensitiveParameters' => [
        'password',
        'secret',
    ],
],
```

the logged request data becomes:

```text
..."extra":{"file":"/path/to/some/class.php","line":314,"request":{"identity":"myIdentity","password":"********","passwordConfirm":"********","details":{"secret":"******"}}},...
```

## Custom processor

If the existing processor does not offer enough features, you can create a custom processor.
The custom processor must implement `Dot\ErrorHandler\Extra\Processor\ProcessorInterface` or extend `Dot\ErrorHandler\Extra\Processor\AbstractProcessor`, which already implements `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`.
Once the custom processor is ready, you need to configure **RequestProvider** to use it.
For this, open `dot-errorhandler`'s config file and - under **RequestProvider::class** - set **processor**.**class** to the class string of your custom processor:

```php
RequestProvider::class  => [
    'enabled'   => false,
    'processor' => [
        'class'               => CustomRequestProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
        'sensitiveParameters' => [
            ProcessorInterface::ALL,
        ],
    ],
],
```

Using this, request data will be processed by `CustomRequestProcessor` and logged as provided by this new processor.
