<?php

namespace Ridouchire\GitlabNotificationsDaemon\Issues;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Ridouchire\GitlabNotificationsDaemon\Issues\Issue;

class IssueRepository
{
    public function __construct(
        private Client $gitlab_client,
        private string $project_id
    ) {
    }

    public function findMany(array $filters = []): array
    {
        $query = http_build_query($filters);

        /** @var Response */
        $res = $this->gitlab_client->request('GET', '/api/v4/projects/' . $this->project_id . '/issues?' . $query);

        if ($res->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $body = (string) $res->getBody();
        $json = json_decode($body, true);

        $issues = [];

        foreach ($json as $issue_data) {
            $issues[] = new Issue(
                $issue_data['iid'],
                $issue_data['title'],
                $issue_data['description'],
                $issue_data['author']['username'],
                isset($issue_data['assignee']['username']) ? $issue_data['assignee']['username'] : null,
                $issue_data['state'],
                $issue_data['labels'],
                $issue_data['user_notes_count'],
                $issue_data['web_url']
            );
        }

        return $issues;
    }
}
