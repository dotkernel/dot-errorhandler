# Log server data

Looking at `dot-errorhandler`'s config file, the array found at `ServerProvider::class` allows you to configure the behavior of this provider:

- **enabled**: enabled/disable this provider
- **processor**: an array configuring the data processor to be used by the **ServerProvider**:
    - **class**: data processor class implementing `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`
    - **replacementStrategy**: whether to replace specific server config values completely or partially
    - **sensitiveParameters**: an array of server config names that may contain sensitive information so their value should be masked partially/completely

## Configure provider

By default, **ServerProvider** is disabled.
It can be enabled only by setting **enabled** to **true**.

If **enabled** is set to **true**, your log file will contain an additional field under the `extra` key, called `server`.
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

- use the constant `ProcessorInterface::ALL`, meaning alter all server config values using the strategy specified by the **replacementStrategy**

```php
'sensitiveParameters' => [
    Dot\ErrorHandler\Extra\Processor\ProcessorInterface::ALL,
],
```

- use exact strings to list the server configs for which the values should be altered using the strategy specified by the **replacementStrategy**

```php
'sensitiveParameters' => [
    'SERVER_ADMIN',
],
```

> **ServerProcessor** uses EXACT server config name lookups.
> To alter the value of a server config, you need to specify the exact server config name.

> The config `sensitiveParameters` is case-insensitive.

## Why should I use a processor

Consider the following request server configs:

```text
[
    "SERVER_ADMIN" => "webmaster@localhost",
    "SERVER_SOFTWARE" => "Apache/2.4.62 (AlmaLinux)",
]
```

Without a **ServerProcessor**, (for this example) the key **SERVER_ADMIN** would end up saved in the log file:

```text
..."extra":{"file":"/path/to/some/class.php","line":314,"server":{"SERVER_ADMIN":"webmaster@localhost","SERVER_SOFTWARE":"Apache/2.4.62 (AlmaLinux)"},...
```

But with a properly configured **ServerProcessor**:

```php
'processor' => [
    'class'               => ServerProcessor::class,
    'replacementStrategy' => ReplacementStrategy::Full,
    'sensitiveParameters' => [
        'SERVER_ADMIN',
    ],
],
```

the logged server config data becomes:

```text
..."extra":{"file":"/path/to/some/class.php","line":314,"server":{"SERVER_ADMIN":"*******************","SERVER_SOFTWARE":"Apache/2.4.62 (AlmaLinux)"},...
```

## Special case

There is a special case, the `HTTP_COOKIE` server config value, which is handled differently than the rest of the values.

Let's take an example of an **HTTP_COOKIE** value:

```text
FRONTEND_SESSID=feb21b39f9c54e3a49af1f862acc8300; rememberMe=63560eb4398d21024b32f2fb45dacca512db0bc725149e1371f493063a03e687
```

If the existing **ServerProcessor** is not used, then the log file will contain dangerous data that may compromise user security – in this case exposing the value of both cookies.

> To avoid this issue, the developer should never use **ServerProvider** without a **ServerProcessor** in a production environment.

Depending on **ServerProvider**'s configuration, **ServerProcessor** will partially mask the cookie values:

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
Once the custom processor is ready, you need to configure **ServerProvider** to use it.
For this, open `dot-errorhandler`'s config file and - under **ServerProvider::class** - set **processor**.**class** to the class string of your custom processor:

```php
ServerProvider::class  => [
    'enabled'   => false,
    'processor' => [
        'class'               => CustomServerProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
        'sensitiveParameters' => [
            ProcessorInterface::ALL,
        ],
    ],
],
```

Using this, server config data will be processed by `CustomServerProcessor` and logged as provided by this new processor.
