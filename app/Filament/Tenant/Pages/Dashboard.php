<?php

namespace App\Filament\Tenant\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.tenant.pages.dashboard';

    public function getTitle(): string
    {
        return 'لوحة التحكم';
    }

    public function getHeading(): string
    {
        return 'مرحباً بك في لوحة المستأجرين';
    }

    public function getSubheading(): ?string
    {
        return 'يمكنك من هنا إدارة عقودك ومدفوعاتك ومعلوماتك الشخصية';
    }
}
