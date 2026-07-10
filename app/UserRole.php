<?php

namespace App;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => __('app.enums.user_role.user'),
            self::Admin => __('app.enums.user_role.admin'),
        };
    }
}
