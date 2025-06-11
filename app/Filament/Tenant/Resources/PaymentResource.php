<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Filament\Tenant\Resources\PaymentResource\Pages;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationLabel = 'مدفوعاتي';
    
    protected static ?string $modelLabel = 'دفعة';
    
    protected static ?string $pluralModelLabel = 'المدفوعات';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('contract', function (Builder $query) {
                $query->where('tenant_id', Auth::guard('tenant')->id());
            })
            ->with(['contract.property', 'contract.unit']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الدفعة')
                    ->schema([
                        Forms\Components\TextInput::make('payment_number')
                            ->label('رقم الدفعة')
                            ->disabled(),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('المبلغ')
                                    ->disabled()
                                    ->suffix('JOD'),
                                Forms\Components\DatePicker::make('payment_date')
                                    ->label('تاريخ الدفع')
                                    ->disabled(),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->label('طريقة الدفع')
                                    ->options([
                                        'cash' => 'نقدي',
                                        'bank_transfer' => 'تحويل بنكي',
                                        'check' => 'شيك',
                                        'credit_card' => 'بطاقة ائتمان',
                                    ])
                                    ->disabled(),
                                Forms\Components\Select::make('status')
                                    ->label('حالة الدفع')
                                    ->options([
                                        'paid' => 'مدفوع',
                                        'pending' => 'في الانتظار',
                                        'overdue' => 'متأخر',
                                    ])
                                    ->disabled(),
                            ]),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->disabled(),
                    ]),
                    
                Forms\Components\Section::make('معلومات العقد')
                    ->schema([
                        Forms\Components\TextInput::make('contract.contract_number')
                            ->label('رقم العقد')
                            ->disabled(),
                        Forms\Components\TextInput::make('contract.property.name')
                            ->label('العقار')
                            ->disabled(),
                        Forms\Components\TextInput::make('contract.unit.unit_number')
                            ->label('الوحدة')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('رقم الدفعة')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('contract.contract_number')
                    ->label('رقم العقد')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('contract.property.name')
                    ->label('العقار')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('JOD')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'نقدي',
                        'bank_transfer' => 'تحويل بنكي',
                        'check' => 'شيك',
                        'credit_card' => 'بطاقة ائتمان',
                        default => $state,
                    }),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'overdue',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'مدفوع',
                        'pending' => 'في الانتظار',
                        'overdue' => 'متأخر',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'paid' => 'مدفوع',
                        'pending' => 'في الانتظار',
                        'overdue' => 'متأخر',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقدي',
                        'bank_transfer' => 'تحويل بنكي',
                        'check' => 'شيك',
                        'credit_card' => 'بطاقة ائتمان',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
            ])
            ->defaultSort('payment_date', 'desc');
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
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
