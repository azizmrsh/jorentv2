<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // إزالة كلمة المرور من البيانات المعروضة للأمان
        unset($data['password']);
        unset($data['confirm_password']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // تشفير كلمة المرور الجديدة إذا تم تغييرها
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // إزالة كلمة المرور من البيانات إذا لم يتم تغييرها
            unset($data['password']);
        }

        // إزالة تأكيد كلمة المرور من البيانات
        unset($data['confirm_password']);

        return $data;
    }
}
