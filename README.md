# Send notifications to Discord, Google Chat, Slack and more from Pimcore

It's Pimcore Bundle to send notifications to Discord, Google Chat, Slack, Telegram, Email or SMS from admin panel inside
Pimcore

## Installation

First of all you need to add notifier to your config/config.yaml file otherwise bundle won't install properly

```
framework:
    notifier:
        chatter_transports:
            slack: '%env(SLACK_DSN)%'
            googlechat: '%env(GOOGLECHAT_DSN)%'
            discord: '%env(DISCORD_DSN)%'
            telegram: '%env(TELEGRAM_DSN)%'
        texter_transports:
            smsapi: '%env(SMSAPI_DSN)%'
```

Then you can install and enable the bundle

```
composer require lemonmind/pimcore-message-bundle
bin/console pimcore:bundle:enable LemonMindMessageBundle
```

And finally in your config/config.yaml file you need to add

```
lemon_mind_message:
    classes:
        Pimcore\Model\DataObject\YOUR_CLASS:
            fields_to_send: series,Carclass,country,price,bodystyle,manufacturer
            email_to_send: some@email.com
            sms_to: PHONE_NUMBER
```

where:

- `YOUR_CLASS:` is your class from which notifications will be sent
- `fields_to_send` is your class fields separated with coma without space
- `email_to_send` is e-mail you want to send notification to
- `sms_to` is the phone number to which you want to send the notification

It is possible to add multiple classes from which you can send notifications

#### Example

```
lemon_mind_message:
    classes:
        Pimcore\Model\DataObject\Car:
            fields_to_send: series,Carclass,country,price,bodystyle,manufacturer
            email_to_send: some@email.com
            sms_to: 123456789
        Pimcore\Model\DataObject\Manufacturer:
            fields_to_send: name
```

After correct installation button for sending the notification should be visible only in your class that you defined
earlier
![](docs/img_home.png)

When you click at the button a modal should pop up where you can select where you want to send notification and add
additional information to the message.
![](docs/img_modal.png)

-----------

## Discord

To integrate this bundle with [Discord](https://discord.com/) you need to add dsn in your .env file

```
DISCORD_DSN=discord://TOKEN@default?webhook_id=ID
```

where:

- `TOKEN` is your secure token of the webhook (returned for Incoming Webhooks)
- `ID` is the id of the webhook

### Sample message

![](docs/img_discord_message.png)

-----------

## Google Chat

To integrate this bundle with [Google Chat](https://workspace.google.com/intl/pl/products/chat/) you need to add dsn in
your .env file

```
GOOGLE_CHAT_DSN=googlechat://ACCESS_KEY:ACCESS_TOKEN@default/SPACE?thread_key=THREAD_KEY
```

where:

- `ACCESS_KEY` is your Google Chat webhook access key
- `ACCESS_TOKEN` is your Google Chat webhook access token
- `SPACE` is the Google Chat space
- `THREAD_KEY` is the Google Chat message thread to group messages into a single thread (optional)

### Sample message

![](docs/img_googlechat_message.png)

-----------

## Slack

To integrate this bundle with [Slack](https://slack.com/) you need to add dsn in your .env file

```
SLACK_DSN=slack://TOKEN@default?channel=CHANNEL
```

where:

- `TOKEN`  is your Bot User OAuth Access Token (they begin with xoxb-)
- `CHANNEL`  is a channel, private group, or IM channel to send message to, it can be an encoded ID, or a name.

### Sample message

![](docs/img_slack_message.png)

-----------

## Telegram

To integrate this bundle with [Telegram](https://telegram.org) you need to add dsn in your .env file

```
TELEGRAM_DSN=telegram://TOKEN@default?channel=CHAT_ID
```

where:

- `TOKEN`  is your Telegram token
- `CHAT_ID`  is your Telegram group chat id (starts with -)

You need to contact [BotFather](https://web.telegram.org/z/#93372553) on Telegram to start creating your bot. Then add
the bot to the group where you want it to send messages

### Sample message

![](docs/img_telegram_message.png)

-----------

## E-mail

To integrate this package with e-mail, you need to prepare as with regular Pimcore e-mail.

### Sample message

![](docs/img_email_message.png)

-----------

## SMSAPI

To integrate this package with [SMSAPI](https://www.smsapi.pl/), you need to add dsn in your .env file

```
SMSAPI_DSN=smsapi://TOKEN@default?from=FROM&fast=FAST&test=TEST
```

where:

- `TOKEN` is your API Token (OAuth)
- `FROM` is the sender name
- `FAST` setting this parameter to 1 (default 0) will result in sending message with the highest priority which
  ensures the quickest possible time of delivery. Attention! Fast messages cost more than normal messages.
- `TEST` setting this parameter to 1 (default 0) will result in sending message in test mode (message is
  validated, but not sent).

You can see your account info at https://ssl.smsapi.pl/
