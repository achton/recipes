# Flowdock recipe

This recipe is for posting notifications and status updates in the Inbox of a
Flowdock flow. It requires a [flow token](https://www.flowdock.com/api/authentication#source-token)
which is generated when you set up a [developer application](https://www.flowdock.com/oauth/applications)
in "Shortcut application" mode. Just create a new application, and check the
box - it will display the flow token to you (but only once).

## Installing

Include the Flowdock recipe in your `deploy.php` file:

```php
require 'recipe/flowdock.php';
```

Add hook on deploy:

```php
before('deploy', 'flowdock:notify');
```

## Configuration

- `flowdock_flow_token` - Token from a [source in the flow's Inbox](https://www.flowdock.com/api/authentication#source-token), **required**
- `repository_url` - URL to the Github repo for the application, for direct
  links to the commit being deployed.

## Tasks

- `flowdock:notify` - send deploy message to Flowdock
- `flowdock:notify:success` - send success message to Flowdock and set thread state
- `flowdock:notify:failure` - send failure message to Flowdock and set thread state

## Usage

To notify about beginning of deployment, add this line:

```php
before('deploy', 'flowdock:notify');
```

To notify about successful deployment, add this:

```php
after('success', 'flowdock:notify:success');
```

To notify about a failed deployment, add this too:

```php
after('deploy:failure', 'flowdock:notify:failure');
```
