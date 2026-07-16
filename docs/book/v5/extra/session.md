# Log session data

Looking at `dot-errorhandler`'s config file, the array found at `SessionProvider::class` allows you to configure the behavior of this provider:

- **enabled**: enabled/disable this provider
- **processor**: an array configuring the data processor to be used by the **SessionProvider**:
    - **class**: data processor class implementing `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`

## Configure provider

By default, **SessionProvider** is disabled.
It can be enabled only by setting **enabled** to **true**.

If **enabled** is set to **true**, your log file will contain an additional field under the `extra` key, called `session`.
If **enabled** is set to **false**, no additional field is added under the `extra` key.

## Configure processor

From here, we assume that **enabled** is set to **true**.

If **processor** is missing/empty, the processor is ignored; the provider will log the raw data available.
If **processor** is specified, but **class** is missing/invalid, the processor is ignored and the provider will log the raw data available.

From here, we assume that **processor**.**class** is valid.

### Replacement strategy

**SessionProcessor** does not use **replacementStrategy**.

> If you create a custom session processor, you may add **replacementStrategy** to the config file; it will get passed to your processor.

### Sensitive parameters

**SessionProcessor** does not use **sensitiveParameters**.

> If you create a custom session processor, you may add **sensitiveParameters** to the config file; it will get passed to your processor.

## Why should I use a processor

The only advantage of using **SessionProcessor** is to reduce the size of the session data that you log.
In the below example, **$_SESSION** contains an **array** in _SessionContainer1_ and an **ArrayObject** in _SessionContainer2_.

```text
    "SessionContainer1" => array:3 [▼
      "_REQUEST_ACCESS_TIME" => 1739973274.7284
      "_VALID" => array:1 [▼
        "Laminas\Session\Validator\Id" => "feb21b39f9c54e3a49af1f862acc8300"
      ]
      "Default" => array:1 [▼
        "EXPIRE" => 1739969278
      ]
    ]
    "SessionContainer2" => Laminas\Stdlib\ArrayObject {#795 ▼
      #storage: array:2 [▼
        "tokenList" => array:1 [▼
          "b222251fb72d49ae0643bff11e11057d" => "389b5e5b415409abb61cfe718e7841bf"
        ]
        "hash" => "389b5e5b415409abb61cfe718e7841bf-b222251fb72d49ae0643bff11e11057d"
      ]
      #flag: 2
      #iteratorClass: "ArrayIterator"
      #protectedProperties: array:4 [▼
        0 => "storage"
        1 => "flag"
        2 => "iteratorClass"
        3 => "protectedProperties"
      ]
    }
```

By using **SessionProcessor**, the two items are reduced to:

```text
    "SessionContainer1" => array:3 [▼
      "_REQUEST_ACCESS_TIME" => 1739973274.7284
      "_VALID" => array:1 [▼
        "Laminas\Session\Validator\Id" => "feb21b39f9c54e3a49af1f862acc8300"
      ]
      "Default" => array:1 [▼
        "EXPIRE" => 1739969278
      ]
    ]
    "SessionContainer2" => array:2 [▼
      "tokenList" => array:1 [▼
        "b222251fb72d49ae0643bff11e11057d" => "389b5e5b415409abb61cfe718e7841bf"
      ]
      "hash" => "389b5e5b415409abb61cfe718e7841bf-b222251fb72d49ae0643bff11e11057d"
    ]
```

As you can see, with arrays the difference is almost ignorable, but with ArrayObjects, the difference is significant.

## Custom processor

If the existing processor does not offer enough features, you can create a custom processor.
The custom processor must implement `Dot\ErrorHandler\Extra\Processor\ProcessorInterface` or extend `Dot\ErrorHandler\Extra\Processor\AbstractProcessor`, which already implements `Dot\ErrorHandler\Extra\Processor\ProcessorInterface`.
Once the custom processor is ready, you need to configure **SessionProvider** to use it.
For this, open `dot-errorhandler`'s config file and - under **SessionProvider::class** - set **processor**.**class** to the class string of your custom processor:

```php
SessionProvider::class  => [
    'enabled'   => false,
    'processor' => [
        'class'               => CustomSessionProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
        'sensitiveParameters' => [
            ProcessorInterface::ALL,
        ],
    ],
],
```

Using this, cookie data will be processed by `CustomSessionProcessor` and logged as provided by this new processor.
