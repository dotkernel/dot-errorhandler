# Extra data

`dot-errorhandler` provides the following data:

- **cookie**: an array containing the request cookies
- **header**: an array containing the request headers
- **request**: an array containing the request body (**\$_PATCH**/**\$_POST**/**\$_PUT**)
- **server**: an array containing the **\$_SERVER** values
- **session**: an array containing the **\$_SESSION** values
- **trace**: an array containing the full stack trace of the request

## Configuring extra data

At this point, you should already have `dot-errorhandler` configured.
If not, proceed to the [Configuration](../configuration.md) page and when you're done, return to this page.

In order start logging one or more of the above data, we first need to enable them from the package's config file.
Open the config file and under `dot-errorhandler` locate the `ExtraProvider::CONFIG_KEY` key.
There you will find 6 associative arrays, each array representing a set of data this package can provide:

- **CookieProvider::class**: request cookie information - [how to use](cookie.md)
- **HeaderProvider::class**: request header information - [how to use](header.md)
- **RequestProvider::class**: request body contents - [how to use](request.md)
- **ServerProvider::class**: server information - [how to use](server.md)
- **SessionProvider::class**: session contents - [how to use](session.md)
- **TraceProvider::class**: trace route - [how to use](trace.md)

These providers and their configuration values are all optional.
If any of them is missing from the config file, the provider simply stays disabled and code execution continues normally.
Invalid configuration values are simply ignored, because the purpose of these providers is to log the extra data if possible, but not to interfere with the app logic.
That's why it does not throw errors on missing/invalid config values.

> By default, `*Provider` classes are used in fall-through mode; without a processor, they just return the unaltered data they were given.
