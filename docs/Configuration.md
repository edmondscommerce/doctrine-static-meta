# Configuration

For all configuration parameters and defaults, you should look at the [ConfigInterface.php](./../src/ConfigInterface.php)

## Retry Connections

By default, DSM will use a special implementation of the DBAL Connection and Statement objects that will try to reconnect to the database if the connection is lost.

You can configure if this is enabled or not and can also configure the timeout limit and max number of retry attempts.

