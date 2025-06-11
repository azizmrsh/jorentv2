<?php

namespace App\Filament\Tenant\Resources\ContractResource\Pages;

use App\Filament\Tenant\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // المستأجرون لا يستطيعون إنشاء عقود جديدة
        ];
    }

    public function getTitle(): string
    {
        return 'عقودي';
    }
}
