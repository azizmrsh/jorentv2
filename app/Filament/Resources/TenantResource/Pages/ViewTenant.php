<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

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

            // تغيير الحالة
            Actions\Action::make('toggle_status')
                ->label(fn () => $this->record->status === 'active' ? __('general.Deactivate') : __('general.Activate'))
                ->icon(fn () => $this->record->status === 'active' ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                ->color(fn () => $this->record->status === 'active' ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->record->status === 'active' ? __('general.Deactivate Tenant') : __('general.Activate Tenant'))
                ->modalDescription(fn () => $this->record->status === 'active' 
                    ? __('general.Are you sure you want to deactivate this tenant?')
                    : __('general.Are you sure you want to activate this tenant?'))
                ->action(function () {
                    $this->record->update([
                        'status' => $this->record->status === 'active' ? 'inactive' : 'active'
                    ]);
                    Notification::make()
                        ->title($this->record->status === 'active' 
                            ? __('general.Tenant activated successfully')
                            : __('general.Tenant deactivated successfully'))
                        ->success()
                        ->send();
                }),
            
            Actions\DeleteAction::make(),
        ];
    }
} 