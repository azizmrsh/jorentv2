<?php

namespace App\Filament\Tenant\Resources\PaymentResource\Pages;

use App\Filament\Tenant\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // يمكن إضافة إجراءات مثل تحميل إيصال الدفع
        ];
    }

    public function getTitle(): string
    {
        return 'عرض الدفعة - ' . $this->record->payment_number;
    }
}
