<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // إزالة كلمة المرور من البيانات المعروضة في النموذج
        unset($data['password']);
        unset($data['confirm_password']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // تشفير كلمة المرور فقط إذا تم تعديلها
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // إزالة كلمة المرور من البيانات إذا لم يتم تعديلها
            unset($data['password']);
        }

        // إزالة تأكيد كلمة المرور من البيانات
        unset($data['confirm_password']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
