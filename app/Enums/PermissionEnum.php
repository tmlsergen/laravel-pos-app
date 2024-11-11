<?php

namespace App\Enums;

enum PermissionEnum: string
{
    use BaseEnum;

    case VIEW_TRANSACTION = 'view transaction';
    case DELETE_TRANSACTION = 'delete transaction';
    case EDIT_POS = 'edit pos';
    case DELETE_POS = 'delete pos';
    case VIEW_POS = 'view pos';
    case VIEW_USER = 'view user';
    case DELETE_USER = 'delete user';
    case EDIT_USER = 'edit user';
    case MAKE_PAYMENT = 'make payment';
}
