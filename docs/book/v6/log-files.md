# Log files

## What is a log file

Log files are plain text files, containing rows of log activity, each row being a standalone activity formatted as specified in the logger's configuration.
With the logger configured in `config/log.global.php.dist`, log activities are formatted with JSON, so each row should be a decodable JSON string.

## What is in a log file

With that configuration, each row in a log file contains the following values:

- **timestamp**: string representation of the date and time when the error occurred
- **level**: numeric representation of the error level
- **levelName**: string representation of the error level, `error` for errors logged by this package
- **message**: error message describing the error
- **context**: an array of extra information that may help the developer debug the error:
    - **file**: the file in which the error occurred
    - **line**: the line from **file** where the error occurred
    - **trace**: the stack trace, because `config/error-handling.global.php.dist` enables the trace provider

For example:

```text
{"timestamp":"2026-11-11T16:05:23+00:00","level":3,"levelName":"error","message":"Something went wrong","context":{"file":"/path/to/some/class.php","line":24,"trace":["Path\\To\\Some\\Class->someMethod:24"]}}
```

By leveraging `dot-errorhandler`'s extra providers, you can also log additional request parameters.
Learn more about what additional parameters are available on the [extra data](extra/introduction.md) page.

> For a clear separation between different types of logs, we recommend that you store files from each type in their own directory.
