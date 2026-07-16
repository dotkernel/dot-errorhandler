# Log header data

Looking at `dot-errorhandler`'s config file, the array found at `HeaderProvider::class` allows you to configure the behavior of this provider:

- **enabled**: enabled/disable this provider
- **processor**: an array configuring the data processor to be used by the **HeaderProvider**:
    - **class**: data processor class implementing `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`
    - **replacementStrategy**: whether to replace specific header values completely or partially
    - **sensitiveParameters**: an array of header names that may contain sensitive information so their value should be masked partially/completely

## Configure provider

By default, **HeaderProvider** is disabled.
It can be enabled only by setting **enabled** to **true**.

If **enabled** is set to **true**, your log file will contain an additional field under the `extra` key, called `header`.
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

- use the constant `ProcessorInterface::ALL`, meaning alter all header values using the strategy specified by the **replacementStrategy**

```php
'sensitiveParameters' => [
    Dot\ErrorHandler\Extra\Processor\ProcessorInterface::ALL,
],
```

- use exact strings to list the headers for which the values should be altered using the strategy specified by the **replacementStrategy**

```php
'sensitiveParameters' => [
    'Authorization',
],
```

> **HeaderProcessor** uses EXACT header name lookups.
> To alter the value of a header, you need to specify the exact header name.

> The config `sensitiveParameters` is case-insensitive.

## Why should I use a processor

Consider the following request headers:

```text
[
    "Authorization" => "Bearer 63560eb4398d21024b32f2fb45dacca512db0bc725149e1371f493063a03e687",
    "Content-Type" => "application/json",
]
```

Without a **HeaderProcessor**, the plain text auth token would end up saved in the log file:

```text
..."extra":{"file":"/path/to/some/class.php","line":314,"header":{"Authorization":"Bearer 63560eb4398d21024b32f2fb45dacca512db0bc725149e1371f493063a03e687","Content-Type":"application/json"},...
```

But with a properly configured **HeaderProcessor**:

```php
'processor' => [
    'class'               => HeaderProcessor::class,
    'replacementStrategy' => ReplacementStrategy::Full,
    'sensitiveParameters' => [
        'Authorization',
    ],
],
```

the logged header data becomes:

```text
..."extra":{"file":"/path/to/some/class.php","line":314,"header":{"Authorization":"***********************************************************************","Content-Type":"application/json"},...
```

## Special case

There is a special case, the `cookie` header, which is handled differently than the rest of the headers.

Let's take an example of a cookie header:

```text
FRONTEND_SESSID=feb21b39f9c54e3a49af1f862acc8300; rememberMe=63560eb4398d21024b32f2fb45dacca512db0bc725149e1371f493063a03e687
```

If the existing **HeaderProcessor** is not used, then the log file will contain dangerous data that may compromise user security – in this case exposing the value of both cookies.

> To avoid this issue, the developer should never use **HeaderProvider** without a **HeaderProcessor** in a production environment.

Depending on **HeaderProvider**'s configuration, **HeaderProcessor** will partially mask the cookie values:

```text
FRONTEND_SESSID=feb21b39f9c54e3a****************; rememberMe=63560eb4398d21024b32f2******************************************
```

when using `ReplacementStrategy::Partial` or completely mask the cookie values:

```text
FRONTEND_SESSID=********************************; rememberMe=****************************************************************
```

when using `ReplacementStrategy::Full`.

## Custom processor

If the existing processor does not offer enough features, you can create a custom processor.
The custom processor must implement `Dot\ErrorHandler\Extra\Processor\ProcessorInterface` or extend `Dot\ErrorHandler\Extra\Processor\AbstractProcessor`, which already implements `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`.
Once the custom processor is ready, you need to configure **HeaderProvider** to use it.
For this, open `dot-errorhandler`'s config file and - under **HeaderProvider::class** - set **processor**.**class** to the class string of your custom processor:

```php
HeaderProvider::class  => [
    'enabled'   => false,
    'processor' => [
        'class'               => CustomHeaderProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
        'sensitiveParameters' => [
            ProcessorInterface::ALL,
        ],
    ],
],
```

Using this, header data will be processed by `CustomHeaderProcessor` and logged as provided by this new processor.
