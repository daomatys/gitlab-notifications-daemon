<?php

require_once __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Loop;
use GuzzleHttp\Client;
use Ridouchire\GitlabNotificationsDaemon\Issues\IssueRepository;
use Ridouchire\GitlabNotificationsDaemon\Pipelines\PipelineRepository;
use Ridouchire\GitlabNotificationsDaemon\Services\TelegramSender;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$gitlab_token    = $_ENV['GITLAB_TOKEN'];
$gitlab_url      = $_ENV['GITLAB_URL'];
$gitlab_username = $_ENV['GITLAB_USERNAME'];
$project_id      = $_ENV['GITLAB_PROJECT_ID'];
$tlgram_token    = $_ENV['TELEGRAM_BOT_TOKEN'];
$user_chat_id    = $_ENV['TELEGRAM_USER_CHAT_ID'];

$telegram_sender = new TelegramSender($tlgram_token, $user_chat_id);

$http_client = new Client([
    'base_uri' => $gitlab_url,
    'headers' => [
        'PRIVATE-TOKEN' => $gitlab_token
    ],
    'allow_redirects' => true
]);

$issue_repo = new IssueRepository($http_client, $project_id);
$pipeline_repo = new PipelineRepository($http_client, $project_id);

$templater = new Environment(new FilesystemLoader([
    __DIR__ . '/user_message_templates',
    __DIR__ . '/message_templates'
]));

$timestamp = time();

Loop::addPeriodicTimer(30, function () use (&$timestamp, $issue_repo, $pipeline_repo, $templater, $telegram_sender, $gitlab_username) {
    $timestamp_str = date('Y-m-d H:i:s', $timestamp);

    $new_issues = $issue_repo->findMany([
        'state'         > 'opened',
        'per_page'      => 10,
        'created_after' => $timestamp_str
    ]);

    foreach ($new_issues as $issue) {
        $text = $templater->render('new_issue.twig', $issue->jsonSerialize());

        $telegram_sender->send($text);
    }

    $assignee_issues = $issue_repo->findMany([
        'state'             => 'opened',
        'assignee_username' => [
            $gitlab_username
        ],
        'per_page'          => 10,
        'updated_after'     => $timestamp_str
    ]);

    foreach ($assignee_issues as $issue) {
        $text = $templater->render('assignee_issue.twig', $issue->jsonSerialize());

        $telegram_sender->send($text);
    }

    $pipelines = $pipeline_repo->findMany([
        'username'      => $gitlab_username,
        'status'        => 'failed',
        'per_page'      => 10,
        'updated_after' => $timestamp_str
    ]);

    foreach ($pipelines as $pipeline) {
        $text = $templater->render('pipeline_failed.twig', $pipeline->jsonSerialize());

        $telegram_sender->send($text);
    }

    # TODO: получать новые комментарии из задач, в которых я являюсь участником
    # TODO: получать новые MR, созданные мною, и отслеживать их состояние
    # TODO: поиск задач по тегам

    $timestamp = time();
});
