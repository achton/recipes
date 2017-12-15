<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Deployer;

use Deployer\Task\Context;
use Deployer\Utility\Httpie;

// Title of project
set('flowdock_title', function () {
    return get('application', 'Project');
});
// Set the Flowdock API URL.
set('flowdock_api_url', 'https://api.flowdock.com/messages');
// Fetch deploy username from Git.
set('user', function () {
    return runLocally('git config --get user.name');
});

// Git commit message.
set('flowdock_message', function () {
    return runLocally('git log -n 1 --format="%an: %s" | tr \'"\' "\'"');
});
// Git revision.
set('flowdock_revision', function () {
    return runLocally('git log -n 1 --format="%h"');
});
set('flowdock_github_url', '{{repository_url}}/commit/{{flowdock_revision}}');

// Determine a unique ID for this deploy configuration, for threading in FD.
set('thread_uuid', function() {
    $host = Context::get()->getHost()->getHostname();
    $string = $host . get('flowdock_revision') . get('flowdock_flow_token');
    return md5($string);
});

// Color of attachment
set('flowdock_deploy_color', 'blue');
set('flowdock_success_color', 'green');
set('flowdock_failure_color', 'red');

// Deploy messages
set('thread_title', 'Deployment to {{hostname}} on {{stage}}');
set('flowdock_deploy_text', 'began deployment');
set('flowdock_success_text', 'successfully completed deployment');
set('flowdock_failure_text', 'FAILED deployment');

// Helper to build request data for Flowdock's API..
function flowdata($type = 'deploy') {
    return [
        'flow_token' => get('flowdock_flow_token'),
        'event' => 'activity',
        'author' => [
            'name' => 'Deployer (' . get('user') . ')',
        ],
        'title' => get('flowdock_' . $type . '_text'),
        'external_thread_id' => get('thread_uuid'),
        'thread' => [
            'title' => get('thread_title'),
            'fields' => [
                ['label' => 'Git revision', 'value' => '<a href="' . get('flowdock_github_url') . '">' . get('flowdock_revision') . '</a>'],
                ['label' => 'Git comment', 'value' => get('flowdock_message')],
            ],
            'external_url' => get('flowdock_github_url'),
            'status' => [
                'color' => get('flowdock_' . $type . '_color'),
                'value' => $type,
            ]
        ]
    ];
}

// Task definitions
desc('Notify Flowdock about deploy start');
task('flowdock:notify', function () {
    if (!get('flowdock_flow_token')) {
        return;
    }
    $data = flowdata();
    Httpie::post(get('flowdock_api_url'))->body($data)->send();
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notify Flowdock about deploy finish');
task('flowdock:notify:success', function () {
    if (!get('flowdock_flow_token')) {
        return;
    }
    $data = flowdata('success');
    Httpie::post(get('flowdock_api_url'))->body($data)->send();
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notify Flowdock about deploy failure');
task('flowdock:notify:failure', function () {
    if (!get('flowdock_flow_token')) {
        return;
    }
    $data = flowdata('failure');
    Httpie::post(get('flowdock_api_url'))->body($data)->send();
})
    ->once()
    ->shallow()
    ->setPrivate();
