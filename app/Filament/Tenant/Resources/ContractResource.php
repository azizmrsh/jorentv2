<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Contract1;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Filament\Tenant\Resources\ContractResource\Pages;

class ContractResource extends Resource
{
    protected static ?string $model = Contract1::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'عقودي';
    
    protected static ?string $modelLabel = 'عقد';
    
    protected static ?string $pluralModelLabel = 'العقود';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->with(['property', 'unit', 'payments']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات العقد')
                    ->schema([
                        Forms\Components\TextInput::make('contract_number')
                            ->label('رقم العقد')
                            ->disabled(),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('تاريخ البداية')
                                    ->disabled(),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('تاريخ الانتهاء')
                                    ->disabled(),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('rent_amount')
                                    ->label('مبلغ الإيجار')
                                    ->disabled()
                                    ->suffix('JOD'),
                                Forms\Components\Select::make('status')
                                    ->label('حالة العقد')
                                    ->options([
                                        'active' => 'نشط',
                                        'expired' => 'منتهي',
                                        'terminated' => 'مفسوخ',
                                    ])
                                    ->disabled(),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('معلومات العقار')
                    ->schema([
                        Forms\Components\TextInput::make('property.name')
                            ->label('اسم العقار')
                            ->disabled(),
                        Forms\Components\TextInput::make('unit.unit_number')
                            ->label('رقم الوحدة')
                            ->disabled(),
                        Forms\Components\Textarea::make('property.address.full_address')
                            ->label('العنوان')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('رقم العقد')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('العقار')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('unit.unit_number')
                    ->label('الوحدة')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('rent_amount')
                    ->label('مبلغ الإيجار')
                    ->money('JOD')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => 'terminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'expired' => 'منتهي',
                        'terminated' => 'مفسوخ',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'expired' => 'منتهي',
                        'terminated' => 'مفسوخ',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'view' => Pages\ViewContract::route('/{record}'),
        ];
    }
}
