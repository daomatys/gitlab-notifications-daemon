<?php

namespace Ridouchire\GitlabNotificationsDaemon\Issues;

class Issue implements \JsonSerializable
{
    public function __construct(
        private int $id,
        private string $title,
        private string $description,
        private string $author,
        private ?string $assignee,
        private string $state,
        private array $labels,
        private int $comment_count,
        private string $web_url,
        private string $created_at,
        private string $updated_at,
        private ?array $milestone,
        private array $time_stats
    ) {
    }

    public function __get(string $key): mixed
    {
        if (in_array($key, array_keys(get_object_vars($this)))) {
            return $this->$key;
        }

        throw new \InvalidArgumentException("{$key} not found");
    }

    public function __set(string $key, mixed $value)
    {
    }

    public function jsonSerialize(): array
    {
        $data = get_object_vars($this);

        $data['labels'] = implode(',', $data['labels']);

        return $data;
    }
}
