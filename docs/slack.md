# Slack recipe

## Installing

<a href="https://slack.com/oauth/authorize?&client_id=113734341365.225973502034&scope=incoming-webhook"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" /></a>

Require slack recipe in your `deploy.php` file:

```php
require 'recipe/slack.php';
```

Add hook on deploy:
 
```php
before('deploy', 'slack:notify');
```

## Configuration

- `slack_webhook` – slack incoming webhook url, **required** 
- `slack_title` – the title of application, default `{{application}}`
- `slack_text` – notification message template, markdown supported
  ```
  _{{user}}_ deploying `{{branch}}` to *{{target}}*
  ```
- `slack_success_text` – success template, default:
  ```
  Deploy to *{{target}}* successful
  ```
- `slack_failure_text` – failure template, default:
  ```
  Deploy to *{{target}}* failed
  ```

- `slack_color` – color's attachment
- `slack_success_color` – success color's attachment
- `slack_failure_color` – failure color's attachment

## Tasks

- `slack:notify` – send message to slack
- `slack:notify:success` – send success message to slack
- `slack:notify:failure` – send failure message to slack

## Usage

If you want to notify only about beginning of deployment add this line only:

```php
before('deploy', 'slack:notify');
```

If you want to notify about successful end of deployment add this too:

```php
after('success', 'slack:notify:success');
```

If you want to notify about failed deployment add this too:

```php
after('deploy:failure', 'slack:notify:failure');
```
