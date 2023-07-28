<?php

namespace Ridouchire\GitlabNotificationsDaemon\Services;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class Templater
{
    private static $twig;

    public static function getTemplater(): Environment
    {
        if (null === self::$twig) {
            $loader = new ArrayLoader([
                'assignee_issue' => <<<EOT
🟢 Назначена задача 🟢 {{ id }}
✏️ {{ title }} ✏️

📜 {{ description }}

📍 Создатель: {{ author }} 📍
🏷 Метки: {{ labels }} 🏷

🌏 {{ web_url }} 🌏
EOT,
                'new_issue' => <<<EOT
⚪️Новая задача⚪️ {{ id }}

✏️ {{ title }} ✏️

📜 {{ description }}

📍 Создатель: {{ author }} 📍
📌 Назначена: {{ assignee }} 📌
🏷 Метки: {{ labels }} 🏷

{{ web_url }}
EOT,
                'pipeline_failed' => <<<EOT
❌ Пайплайн упал ❌ {{ id }}

🌏 {{ web_url }}  🌏
EOT,
                'backend_label_issue' => <<<EOT
🛠 Требуется работа разработчика серверной части приложения {{ id }}

✏️ {{ title }} ✏️

📜 {{ description }}

📍 Создатель: {{ author }} 📍
🏷 Метки: {{ labels }} 🏷

🌏 {{ web_url }} 🌏
EOT
            ]);

            self::$twig = new Environment($loader);
        }

        return self::$twig;
    }
}
