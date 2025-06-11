<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Contract1Resource\Pages;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Traits\FileUploadTrait;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

//osaid 
// Export functionality imports
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;



class Contract1Resource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = Contract1::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $slug = 'contracts';

    public static function getNavigationLabel(): string
    {
        return __('general.Contracts');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Contract');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Contracts');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // القسم الأول: معلومات الأطراف
            Forms\Components\Fieldset::make(__('general.Parties Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('landlord_name')
                                ->label(__('general.Landlord Name'))
                                ->required()
                                ->maxLength(255)
                                ->placeholder(__('general.Enter landlord full name'))
                                ->prefixIcon('heroicon-o-user')
                                ->columnSpan(1),

                            Forms\Components\Select::make('tenant_id')
                                ->label(__('general.Tenant'))
                                ->relationship('tenant', 'firstname')
                                ->searchable()
                                ->required()
                                ->placeholder(__('general.Select tenant'))
                                ->prefixIcon('heroicon-o-users')
                                ->columnSpan(1),
                        ]),
                ])
                ->columns(1),

            // القسم الثاني: تفاصيل العقار والوحدة
            Forms\Components\Fieldset::make(__('general.Property & Unit Details'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('property_id')
                                ->options(Property::all()->pluck('name', 'id'))
                                ->required()
                                ->label(__('general.Property'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->prefixIcon('heroicon-o-building-office-2')
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $property = Property::with('address')->find($state);
                                    if ($property?->address) {
                                        $set('governorate', $property->address->governorate);
                                        $set('city', $property->address->city);
                                        $set('district', $property->address->district);
                                        $set('building_number', $property->address->building_number);
                                        $set('plot_number', $property->address->plot_number);
                                        $set('basin_number', $property->address->basin_number);
                                        $set('property_number', $property->address->property_number);
                                        $set('street_name', $property->address->street_name);
                                    }
                                    $set('unit_id', null);
                                })
                                ->columnSpan(1),

                            Forms\Components\Select::make('unit_id')
                                ->options(fn (callable $get) =>
                                    Unit::where('property_id', $get('property_id'))->pluck('name', 'id')
                                )
                                ->required()
                                ->label(__('general.Unit'))
                                ->searchable()
                                ->prefixIcon('heroicon-o-home')
                                ->placeholder(__('general.Select unit'))
                                ->columnSpan(1),
                        ]),
                    
                    // عنوان العقار
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('governorate')
                                ->label(__('general.Governorate'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-map-pin'),
                            
                            Forms\Components\TextInput::make('city')
                                ->label(__('general.City'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-building-office'),
                            
                            Forms\Components\TextInput::make('district')
                                ->label(__('general.District'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-map'),
                            
                            Forms\Components\TextInput::make('street_name')
                                ->label(__('general.Street Name'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-map-pin'),
                        ]),
                    
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('building_number')
                                ->label(__('general.Building Number'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-hashtag'),
                            
                            Forms\Components\TextInput::make('plot_number')
                                ->label(__('general.Plot Number'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-hashtag'),
                            
                            Forms\Components\TextInput::make('basin_number')
                                ->label(__('general.Basin Number'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-hashtag'),
                            
                            Forms\Components\TextInput::make('property_number')
                                ->label(__('general.Property Number'))
                                ->readOnly()
                                ->prefixIcon('heroicon-o-hashtag'),
                        ]),
                ])
                ->columns(1),

            // القسم الثالث: تفاصيل العقد المالية والزمنية
            Forms\Components\Fieldset::make(__('general.Contract Financial & Time Details'))
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->label(__('general.Start Date'))
                                ->required()
                                ->prefixIcon('heroicon-o-calendar')
                                ->placeholder(__('general.Select start date'))
                                ->live()
                                ->columnSpan(1),
                            
                            Forms\Components\DatePicker::make('end_date')
                                ->label(__('general.End Date'))
                                ->required()
                                ->prefixIcon('heroicon-o-calendar')
                                ->placeholder(__('general.Select end date'))
                                ->live()
                                ->columnSpan(1),
                            
                            Forms\Components\DatePicker::make('due_date')
                                ->label(__('general.Due Date'))
                                ->prefixIcon('heroicon-o-calendar-days')
                                ->placeholder(__('general.Select due date'))
                                ->columnSpan(1),
                        ]),
                    
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('rent_amount')
                                ->label(__('general.Rent Amount'))
                                ->numeric()
                                ->required()
                                ->suffix('JOD')
                                ->prefixIcon('heroicon-o-banknotes')
                                ->placeholder(__('general.Enter rent amount'))
                                ->minValue(0)
                                ->columnSpan(1),
                            
                            Forms\Components\Select::make('status')
                                ->label(__('general.Contract Status'))
                                ->options([
                                    'active' => __('general.Active'),
                                    'inactive' => __('general.Inactive'),
                                    'pending' => __('general.Pending'),
                                    'expired' => __('general.Expired'),
                                ])
                                ->default('active')
                                ->required()
                                ->prefixIcon('heroicon-o-check-circle')
                                ->columnSpan(1),
                        ]),
                ])
                ->columns(1),

            // القسم الرابع: الشروط والأحكام
            Forms\Components\Fieldset::make(__('general.Terms and Conditions'))
                ->schema([
                    Forms\Components\Textarea::make('terms_and_conditions_extra')
                        ->label(__('general.Additional Terms and Conditions'))
                        ->placeholder(__('general.Enter any additional terms and conditions'))
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(1),

            // القسم الخامس: التوقيعات الرقمية
            Forms\Components\Fieldset::make(__('general.Digital Signatures'))
                ->schema([
                    // توقيع المستأجر
                    SignaturePad::make('tenant_signature_path')
                        ->label(__('general.Tenant Signature'))
                        ->required()
                        ->dehydrateStateUsing(function ($state) {
                            if ($state) {
                                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                                $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                                
                                $directory = public_path('uploads/contracts/signatures');
                                if (!is_dir($directory)) {
                                    mkdir($directory, 0755, true);
                                }
                                
                                $publicPath = public_path('uploads/' . $fileName);
                                file_put_contents($publicPath, $imageData);
                                
                                return $fileName;
                            }
                            return null;
                        }),

                    // توقيع المؤجر
                    SignaturePad::make('landlord_signature_path')
                        ->label(__('general.Landlord Signature'))
                        ->required()
                        ->dehydrateStateUsing(function ($state) {
                            if ($state) {
                                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                                $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                                
                                $directory = public_path('uploads/contracts/signatures');
                                if (!is_dir($directory)) {
                                    mkdir($directory, 0755, true);
                                }
                                
                                $publicPath = public_path('uploads/' . $fileName);
                                file_put_contents($publicPath, $imageData);
                                
                                return $fileName;
                            }
                            return null;
                        }),
                ])
                ->columns(2),

            // القسم السادس: معلومات الشهود
            Forms\Components\Fieldset::make(__('general.Witnesses Information'))
                ->schema([
                    Forms\Components\TextInput::make('witness1_name')
                        ->label(__('general.First Witness Name'))
                        ->maxLength(255),
                    
                    SignaturePad::make('witness1_signature_path')
                        ->label(__('general.First Witness Signature'))
                        ->required()
                        ->dehydrateStateUsing(function ($state) {
                            if ($state) {
                                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                                $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                                
                                $directory = public_path('uploads/contracts/signatures');
                                if (!is_dir($directory)) {
                                    mkdir($directory, 0755, true);
                                }
                                
                                $publicPath = public_path('uploads/' . $fileName);
                                file_put_contents($publicPath, $imageData);
                                
                                return $fileName;
                            }
                            return null;
                        }),

                    Forms\Components\TextInput::make('witness2_name')
                        ->label(__('general.Second Witness Name'))
                        ->maxLength(255),
                    
                    SignaturePad::make('witness2_signature_path')
                        ->label(__('general.Second Witness Signature'))
                        ->required()
                        ->dehydrateStateUsing(function ($state) {
                            if ($state) {
                                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                                $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                                
                                $directory = public_path('uploads/contracts/signatures');
                                if (!is_dir($directory)) {
                                    mkdir($directory, 0755, true);
                                }
                                
                                $publicPath = public_path('uploads/' . $fileName);
                                file_put_contents($publicPath, $imageData);
                                
                                return $fileName;
                            }
                            return null;
                        }),
                ])
                ->columns(2),

            // القسم السابع: معلومات إضافية
            Forms\Components\Fieldset::make(__('general.Meta Information'))
                ->schema([
                    Forms\Components\DatePicker::make('hired_date')
                        ->label(__('general.Created Date'))
                        ->default(now())
                        ->disabled(),
                    
                    Forms\Components\TextInput::make('hired_by')
                        ->label(__('general.Created By'))
                        ->default(fn () => Auth::user()?->name)
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('general.Contract ID'))
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('landlord_name')
                    ->label(__('general.Landlord'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('general.Landlord name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label(__('general.Tenant'))
                    ->searchable(['firstname', 'lastname'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->tenant ? $record->tenant->firstname . ' ' . $record->tenant->lastname : __('general.Not specified'))
                    ->copyable()
                    ->copyMessage(__('general.Tenant name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.phone')
                    ->label(__('general.Tenant Phone'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('general.Phone number copied!'))
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label(__('general.Property'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('general.Property name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.name')
                    ->label(__('general.Unit'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage(__('general.Unit name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.rental_price')
                    ->label(__('general.Rental Price'))
                    ->sortable()
                    ->money('JOD')
                    ->alignEnd()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('general.Start Date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('general.End Date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract_duration')
                    ->label(__('general.Contract Duration'))
                    ->getStateUsing(function ($record) {
                        if ($record->start_date && $record->end_date) {
                            $start = \Carbon\Carbon::parse($record->start_date);
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $months = $start->diffInMonths($end);
                            return $months . ' ' . __('general.months');
                        }
                        return __('general.Not specified');
                    })
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'expired' => __('general.Expired'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label(__('general.Days Remaining'))
                    ->getStateUsing(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                return $now->diffInDays($end) . ' ' . __('general.days');
                            }
                            return __('general.Expired');
                        }
                        return __('general.Not specified');
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                $days = $now->diffInDays($end);
                                if ($days <= 30) return 'danger';
                                if ($days <= 90) return 'warning';
                                return 'success';
                            }
                        }
                        return 'gray';
                    })
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_signatures')
                    ->label(__('general.Signatures'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->tenant_signature_path) && !empty($record->landlord_signature_path))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_pdf')
                    ->label(__('general.PDF'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->hasPdf())
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-document-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label(__('general.Created By'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label(__('general.Created Date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Date Added'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label(__('general.Property'))
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label(__('general.Unit'))
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label(__('general.Tenant'))
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.Status'))
                    ->options([
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'expired' => __('general.Expired'),
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('contract_dates')
                    ->label(__('general.Contract Dates'))
                    ->form([
                        Forms\Components\DatePicker::make('start_date_from')
                            ->label(__('general.Start Date From')),
                        Forms\Components\DatePicker::make('start_date_until')
                            ->label(__('general.Start Date Until')),
                        Forms\Components\DatePicker::make('end_date_from')
                            ->label(__('general.End Date From')),
                        Forms\Components\DatePicker::make('end_date_until')
                            ->label(__('general.End Date Until')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_date_from'], fn ($q, $date) => $q->where('start_date', '>=', $date))
                            ->when($data['start_date_until'], fn ($q, $date) => $q->where('start_date', '<=', $date))
                            ->when($data['end_date_from'], fn ($q, $date) => $q->where('end_date', '>=', $date))
                            ->when($data['end_date_until'], fn ($q, $date) => $q->where('end_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date_from'] ?? null) {
                            $indicators['start_date_from'] = 'Start Date From: ' . \Carbon\Carbon::parse($data['start_date_from'])->format('Y-m-d');
                        }
                        if ($data['start_date_until'] ?? null) {
                            $indicators['start_date_until'] = 'Start Date Until: ' . \Carbon\Carbon::parse($data['start_date_until'])->format('Y-m-d');
                        }
                        if ($data['end_date_from'] ?? null) {
                            $indicators['end_date_from'] = 'End Date From: ' . \Carbon\Carbon::parse($data['end_date_from'])->format('Y-m-d');
                        }
                        if ($data['end_date_until'] ?? null) {
                            $indicators['end_date_until'] = 'End Date Until: ' . \Carbon\Carbon::parse($data['end_date_until'])->format('Y-m-d');
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('rental_price_range')
                    ->label(__('general.Rental Price Range'))
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label(__('general.Minimum Price'))
                            ->numeric()
                            ->suffix('JOD'),
                        Forms\Components\TextInput::make('max_price')
                            ->label(__('general.Maximum Price'))
                            ->numeric()
                            ->suffix('JOD'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_price'], function ($q, $price) {
                                return $q->whereHas('unit', fn ($query) => $query->where('rental_price', '>=', $price));
                            })
                            ->when($data['max_price'], function ($q, $price) {
                                return $q->whereHas('unit', fn ($query) => $query->where('rental_price', '<=', $price));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_price'] ?? null) {
                            $indicators['min_price'] = 'Minimum Price: ' . number_format($data['min_price']) . ' JOD';
                        }
                        if ($data['max_price'] ?? null) {
                            $indicators['max_price'] = 'Maximum Price: ' . number_format($data['max_price']) . ' JOD';
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('expiring_soon')
                    ->label(__('general.Contracts Expiring Soon'))
                    ->query(function ($query) {
                        return $query->where('end_date', '>=', now())
                                    ->where('end_date', '<=', now()->addDays(30));
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('with_signatures')
                    ->label('Contracts with Signatures')
                    ->query(function ($query) {
                        return $query->whereNotNull('tenant_signature_path')
                                    ->whereNotNull('landlord_signature_path');
                    })
                    ->toggle(),
                    

            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                   ->label('Export Contracts'),
                //    ->color('success')
                //    ->icon('heroicon-o-arrow-down-tray')
                //    ->fileName('contracts_' . date('Y-m-d'))
                //    ->withColumns([
                //        'id' => 'Contract ID',
                //        'landlord_name' => 'Landlord',
                //        'tenant_name' => 'Tenant Name',
                //        'tenant_phone' => 'Tenant Phone',
                //        'tenant_email' => 'Tenant Email',
                //        'property_name' => 'Property',
                //        'unit_name' => 'Unit',
                //        'rental_price' => 'Rental Price',
                //        'start_date' => 'Start Date',
                //        'end_date' => 'End Date',
                //        'status' => 'Status',
                //        'hired_by' => 'Created By',
                //        'hired_date' => 'Creation Date',
                //        'created_at' => 'Added Date',
                //    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('general.View'))
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label(__('general.Edit'))
                    ->color('warning'),
                    
                // View PDF Action
                Tables\Actions\Action::make('view_pdf')
                    ->label(__('general.View PDF'))
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn (Contract1 $record): string => $record->pdf_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Contract1 $record): bool => $record->hasPdf()),
                    
                // Generate/Regenerate PDF Action
                Tables\Actions\Action::make('generate_pdf')
                    ->label(fn (Contract1 $record): string => $record->hasPdf() ? __('general.Regenerate PDF') : __('general.Generate PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Contract1 $record) {
                        $contractPdfService = new \App\Services\ContractPdfService();
                        $pdfPath = $contractPdfService->regenerateContractPdf($record);
                        
                        if ($pdfPath) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('general.PDF Generated Successfully'))
                                ->body(__('general.Contract PDF has been generated and saved.'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title(__('general.PDF Generation Failed'))
                                ->body(__('general.There was an error generating the PDF. Please try again.'))
                                ->danger()
                                ->duration(7000)
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Generate Contract PDF'))
                    ->modalDescription(__('general.This will generate a PDF version of the contract. Are you sure?')),
                    
                Tables\Actions\DeleteAction::make()
                    ->label(__('general.Delete'))
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('general.Delete Selected'))
                        ->color('danger'),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected')),
                    //    ->color('success')
                    //    ->icon('heroicon-o-arrow-down-tray')
                    //    ->fileName('selected_contracts_' . date('Y-m-d'))
                    //    ->withColumns([
                    //        'id' => 'Contract ID',
                    //        'landlord_name' => 'Landlord',
                    //        'tenant_name' => 'Tenant Name',
                    //        'tenant_phone' => 'Tenant Phone',
                    //        'property_name' => 'Property',
                    //        'unit_name' => 'Unit',
                    //        'rental_price' => 'Rental Price',
                    //        'start_date' => 'Start Date',
                    //        'end_date' => 'End Date',
                    //        'status' => 'Status',
                    //        'created_at' => 'Added Date',
                    //    ]),
                ]),
            ])
            ->emptyStateHeading('No Contracts Found')
            ->emptyStateDescription('Start by creating a new contract.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContract1s::route('/'),
            'create' => Pages\CreateContract1::route('/create'),
            'edit' => Pages\EditContract1::route('/{record}/edit'),
           //'view' => Pages\ViewContract1::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
}