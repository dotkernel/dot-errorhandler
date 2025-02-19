# Log files

## What is a log file

Log files are plain text files, containing rows of log activity, each row being a standalone activity formatted as specified in `dot-errorhandler`'s configuration file.
By default, log activities are formatted with JSON, so each row should be a decodable JSON string.

## What is in a log file

Each row in a log file should contain the following values:

- **timestamp**: string representation of the date and time when the error occurred
- **priority**: numeric representation of the error level
- **priorityName**: string representation of the error level
- **message**: error message describing the error
- **extra**: an array of extra information that may help the developer debug the error:
  - **file**: the file in which the error occurred
  - **line**: the line from **file** where the error occurred

By leveraging `dot-errorhandler`'s extra providers, you can also log additional request parameters.
Learn more about what additional parameters are available on the [extra data](extra-data.md) page.
