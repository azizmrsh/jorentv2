<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            // إرسال رابط التحقق
            Actions\Action::make('resend_verification')
                ->label(__('general.Resend Verification'))
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->visible(fn () => is_null($this->record->email_verified_at))
                ->action(function () {
                    $this->record->sendEmailVerificationNotification();
                    Notification::make()
                        ->title(__('general.Verification email sent'))
                        ->body(__('general.Verification link sent to') . ' ' . $this->record->email)
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading(__('general.Resend Email Verification Link'))
                ->modalDescription(__('general.Are you sure you want to resend the verification link?'))
                ->modalSubmitActionLabel(__('general.Send')),

            // تأكيد البريد يدوياً
            Actions\Action::make('mark_verified')
                ->label(__('general.Mark as Verified'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => is_null($this->record->email_verified_at))
                ->action(function () {
                    $this->record->update(['email_verified_at' => now()]);
                    Notification::make()
                        ->title(__('general.Email Verified Successfully'))
                        ->body(__('general.Email verified for') . ' ' . $this->record->email)
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading(__('general.Manually Verify Email'))
                ->modalDescription(__('general.Are you sure you want to manually verify the email?'))
                ->modalSubmitActionLabel(__('general.Verify')),

            Actions\DeleteAction::make(),
        ];
    }
} 