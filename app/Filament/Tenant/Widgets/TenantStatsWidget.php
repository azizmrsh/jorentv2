<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TenantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenantId = Auth::guard('tenant')->id();
        
        // حصائيات العقود
        $activeContracts = Contract1::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();
            
        $totalContracts = Contract1::where('tenant_id', $tenantId)->count();
        
        // إحصائيات المدفوعات
        $totalPayments = Payment::whereHas('contract', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->where('status', 'paid')->sum('amount');
        
        $pendingPayments = Payment::whereHas('contract', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->where('status', 'pending')->count();
        
        $overduePayments = Payment::whereHas('contract', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->where('status', 'overdue')->count();

        return [
            Stat::make('العقود النشطة', $activeContracts)
                ->description('من أصل ' . $totalContracts . ' عقد')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
                
            Stat::make('إجمالي المدفوعات', number_format($totalPayments, 2) . ' JOD')
                ->description('المبلغ المدفوع')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
                
            Stat::make('المدفوعات المعلقة', $pendingPayments)
                ->description('في انتظار المعالجة')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('المدفوعات المتأخرة', $overduePayments)
                ->description('تحتاج إلى انتباه')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
