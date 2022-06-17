# Loogger Notification Driver

## Description

## Install

```bash
composer require efureev/loogger "^1.0"
```

## Use

### In your code:

```php
$looggerConfig = new Config($looggerConfigJson);
$loogger = new Loogger($looggerConfig);

$loogger->send($msg);
// ...
$loogger
    ->pattern("<b>Gitlab</b>: {{MSG}}")
    ->sendHTML("<i>Hello from service</i>");
```

A Message type format:

```php
$loogger
->asPlain() // send message as plain
// ->send('Text')
->asHTML() // send message as HTML
// ->send('<b>Bold</b> <pre>code</pre>')
->asMD() // send message as Markdown.
// ->send('*Bold* text')
```

Allowed variables for patterns and message:

- `MSG` - The Message content
- `SERVICE_NAME` - The service name
- `SERVICE_DESCRIPTION` - The service description
- `NOW` - The datetime. Format: `RFC822`
- `CHAT_ID` - The TG-chat ID
- `BR` - Return a cursor to the next line. Like a `\n`.
- `TAB` - Return a cursor to the next line. Like a `\t`.

### from console-file:

```shell
php send2loogger.php --msg='Testing message!'
```

**Arguments:**

* `--cnf=<path>` - A full path to config file. Config file should be a JSON typed.
* `--msg='<text>'` - A sending message
* `--debug` - Enables `debug mode`

### from curl:

```shell
curl --location --request POST 'https://loogger.mockery.dev/push' \
--header 'X-Token: $2a$10$du8TEzvOs6HTgrbkuuTl0JNnhGF2Ki3xLKuPG5LoGBcn4S91W' \
--data-raw 'Hello from service'
```

## Use Loogger service

Loogger is a service to send various notifications.
Nowdays, it has only one driver: `Telegram`.

The service located at `https://loogger.mockery.dev`.

To use the Loogger service you should register there through Telegram:

- Add tg-bot `@Loogger` to your group or direct chat to it.
- To register into Loogger:  
  _For person chat_: you should send auth data to it.  
  _For group chat_: nothing.
- Create a service: `/add`

A full list of commands you receive on `/help`
