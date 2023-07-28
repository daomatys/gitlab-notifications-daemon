<?php

namespace Ridouchire\GitlabNotificationsDaemon\Pipelines;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Ridouchire\GitlabNotificationsDaemon\Pipelines\Pipeline;

class PipelineRepository
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
        $res = $this->gitlab_client->request('GET', '/api/v4/projects/' . $this->project_id . '/pipelines?' . $query);

        if ($res->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $body = (string) $res->getBody();
        $json = json_decode($body, true);

        $pipelines = [];

        foreach ($json as $pipeline_data) {
            $pipelines[] = new Pipeline(
                $pipeline_data['id'],
                $pipeline_data['status'],
                $pipeline_data['web_url']
            );
        }

        return $pipelines;
    }
}
