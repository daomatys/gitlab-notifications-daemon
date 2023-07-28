<?php

namespace Ridouchire\GitlabNotificationsDaemon;

enum Types: string
{
    case issue         = 'issue';
    case merge_request = 'merge_request';
    case pipeline      = 'pipeline';
}
