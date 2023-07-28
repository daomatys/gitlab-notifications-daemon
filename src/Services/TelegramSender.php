<?php

namespace Ridouchire\GitlabNotificationsDaemon\Services;

use GuzzleHttp\Client;

class TelegramSender
{
    private const URL = 'https://api.telegram.org/bot[bot_token]/sendMessage?chat_id=[user_chat_id]&text=[message]';

    public function __construct(
        private string $bot_token,
        private string $user_chat_id
    ) {
        $this->client = new Client();
    }

    public function send(string $message): void
    {
        $this->client->post(str_replace(['[bot_token]', '[user_chat_id]', '[message]'], [$this->bot_token, $this->user_chat_id, $message], self::URL));
    }
}
