# Extra data

`dot-errorhandler` provides the following data:

- **cookie**: an array containing the request cookies
- **header**: an array containing the request headers
- **request**: an array containing the parsed request body, for example form fields or a decoded JSON body
- **server**: an array containing the server parameters (**\$_SERVER**)
- **session**: an array containing the **\$_SESSION** values
- **trace**: an array containing the full stack trace of the request

## Configuring extra data

At this point, you should already have `dot-errorhandler` configured.
If not, proceed to the [Configuration](../configuration.md) page, and when you're done, return to this page.

To start logging one or more of the above data, we first need to enable them from the package's config file.
Open the config file and under `dot-errorhandler` locate the `ExtraProvider::CONFIG_KEY` key.
There you will find six associative arrays, each array representing a set of data this package can provide:

- **CookieProvider::class**: request cookie information – [how to use](cookie.md)
- **HeaderProvider::class**: request header information – [how to use](header.md)
- **RequestProvider::class**: request body contents – [how to use](request.md)
- **ServerProvider::class**: server information – [how to use](server.md)
- **SessionProvider::class**: session contents – [how to use](session.md)
- **TraceProvider::class**: trace route – [how to use](trace.md)

These providers and their configuration values are all optional.
If any of them is missing from the config file, the provider simply stays disabled and code execution continues normally.

A provider is enabled only when its **enabled** value is exactly `true`.
Any other value, including `1`, `'yes'` or `null`, leaves it disabled.

Invalid configuration values are ignored, because the purpose of these providers is to log the extra data if possible but not to interfere with the app logic:

- provider options that are not an array leave the provider disabled
- a **processor**.**class** that does not implement `Dot\ErrorHandler\Extra\Processor\ProcessorInterface` is ignored, and the provider logs the raw data
- **sensitiveParameters** entries that are not strings or integers are ignored
- a **replacementStrategy** that is not a `Dot\ErrorHandler\Extra\ReplacementStrategy` falls back to `ReplacementStrategy::Full`

> By default, `*Provider` classes are used in fall-through mode; without a processor, they just return the unaltered data they were given.

> Masking counts characters, not bytes, so masked values stay valid UTF-8.
