<?php

namespace App\Enums;

enum RoleEnum: string
{
    use BaseEnum;

    case ADMIN = 'admin';
    case USER = 'user';
    case SUPPORT = 'support';
}
