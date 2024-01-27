<?php

namespace App\Entity\Enum;


use MyCLabs\Enum\Enum;

class MeetingStatus extends Enum
{
    public const NOT_STARTED = 'NOT_STARTED';
    public const APPROVED = 'APPROVED';
    public const ACTIVE = 'ACTIVE';
    public const COMPLETED = 'COMPLETED';
    public const CANCELED = 'CANCELED';

}