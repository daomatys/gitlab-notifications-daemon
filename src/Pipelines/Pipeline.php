<?php

namespace Ridouchire\GitlabNotificationsDaemon\Pipelines;

class Pipeline implements \JsonSerializable
{
    public function __construct(
        private int $id,
        private string $status,
        private string $web_url,
        private string $created_at,
        private string $updated_at,
        private int $ref,
        private string $sha
    ) {
    }

    public function __get(string $key): mixed
    {
        if (in_array($key, array_keys(get_object_vars($this)))) {
            return $this->$key;
        }

        throw new \InvalidArgumentException("{$key} not found");
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
