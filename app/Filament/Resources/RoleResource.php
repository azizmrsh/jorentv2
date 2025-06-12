<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Resources\RoleResource as BaseRoleResource;

class RoleResource extends BaseRoleResource
{
    public static function getMiddleware(): array
    {
        return [
            'super.admin'
        ];
    }
}
