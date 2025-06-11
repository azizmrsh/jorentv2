<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccResource\Pages;
use App\Models\Acc;
use App\Traits\FileUploadTrait;
use App\Traits\HasNationalityField;
use App\Helpers\NationalityHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Notifications\Notification;



class AccResource extends Resource
{
    use FileUploadTrait, HasNationalityField;
    
    protected static ?string $model = Acc::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $slug = 'property-managers';

    public static function getNavigationLabel(): string
    {
        return __('general.Property Managers');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Property Manager');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Property Managers');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Fieldset::make(__('general.Personal Information'))
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('firstname')
                                ->label(__('general.First Name'))
                                ->required()
                                ->maxLength(255)
                                ->placeholder(__('general.Enter first name'))
                                ->columnSpan(1),
                            
                            Forms\Components\TextInput::make('midname')
                                ->label(__('general.Middle Name'))
                                ->maxLength(255)
                                ->placeholder(__('general.Enter middle name (optional)'))
                                ->columnSpan(1),
                            
                            Forms\Components\TextInput::make('lastname')
                                ->label(__('general.Last Name'))
                                ->maxLength(255)
                                ->placeholder(__('general.Enter last name'))
                                ->columnSpan(1),
                        ]),
                    
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('birth_date')
                                ->label(__('general.Birth Date'))
                                ->required()
                                ->maxDate(now()->subYears(18))
                                ->before(now()->subYears(18))
                                ->live()
                                ->afterStateUpdated(function ($state, $component) {
                                    if ($state && \Carbon\Carbon::parse($state)->age < 18) {
                                        $component->state(null);
                                    }
                                })
                                ->validationMessages([
                                    'before' => __('general.Age must be at least 18 years'),
                                    'max_date' => __('general.Age must be at least 18 years'),
                                ])
                                ->helperText(__('general.You must be at least 18 years old'))
                                ->placeholder(__('general.Select birth date'))
                                ->columnSpan(1),
                            
                            self::nationalityField()
                                ->columnSpan(1),
                        ]),
                ])
                ->columns(3),

            Forms\Components\Fieldset::make(__('general.Contact Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->label(__('general.Email'))
                                ->email()
                                ->maxLength(255)
                                ->required()
                                ->unique(table: 'accs', column: 'email', ignoreRecord: true)
                                ->placeholder(__('general.Enter email address'))
                                ->validationMessages([
                                    'unique' => __('general.This email address is already in use. Please choose another one'),
                                    'email' => __('general.Please enter a valid email address'),
                                    'required' => __('general.The email field is required'),
                                ])
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('phone')
                                ->label(__('general.Phone'))
                                ->required()
                                ->placeholder(__('general.Enter phone number'))
                                ->columnSpan(1),
                        ]),
                    
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('password')
                                ->label(__('general.Password'))
                                ->password()
                                ->maxLength(255)
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrated(fn ($state) => filled($state))
                                ->visible(fn (string $context) => in_array($context, ['create', 'edit']))
                                ->placeholder(__('general.Enter password'))
                                ->helperText(fn (string $context): string => $context === 'edit' ? __('general.Leave empty to keep current password') : '')
                                ->columnSpan(1),
                            
                            Forms\Components\TextInput::make('confirm_password')
                                ->label(__('general.Confirm Password'))
                                ->password()
                                ->maxLength(255)
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrated(fn ($state) => filled($state))
                                ->visible(fn (string $context) => in_array($context, ['create', 'edit']))
                                ->same('password')
                                ->requiredWith('password')
                                ->placeholder(__('general.Confirm password'))
                                ->helperText(fn (string $context): string => $context === 'edit' ? __('general.Leave empty to keep current password') : '')
                                ->columnSpan(1),
                        ]),
                    
                    Forms\Components\TextInput::make('address')
                        ->label(__('general.Address'))
                        ->placeholder(__('general.Enter full address'))
                        ->maxLength(255)
                        ->columnSpan(2),
                ])
                ->columns(2),

            Forms\Components\Fieldset::make(__('general.Profile Information'))
                ->schema([
                    Forms\Components\Grid::make(1)
                        ->schema([
                            self::profilePhotoUpload()
                                ->columnSpan(1),
                            
                            Forms\Components\Select::make('status')
                                ->label(__('general.Status'))
                                ->required()
                                ->options([
                                    'active' => __('general.Active'),
                                    'inactive' => __('general.Inactive'),
                                    'suspended' => __('general.Suspended'),
                                    'pending' => __('general.Pending'),
                                ])
                                ->default('active')
                                ->native(false)
                                ->placeholder(__('general.Select status'))
                                ->columnSpan(1),
                        ]),
                ])
                ->columns(1),

            Forms\Components\Fieldset::make(__('general.Document Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('document_type')
                                ->label(__('general.Document Type'))
                                ->required()
                                ->options([
                                    'passport' => __('general.Passport'),
                                    'id_card' => __('general.ID Card'),
                                    'driver_license' => __('general.Driver License'),
                                    'residency_permit' => __('general.Residency Permit'),
                                    'other' => __('general.Other'),
                                ])
                                ->default('passport')
                                ->native(false)
                                ->placeholder(__('general.Select document type'))
                                ->columnSpan(1),
                            
                            Forms\Components\TextInput::make('document_number')
                                ->label(__('general.Document Number'))
                                ->maxLength(255)
                                ->placeholder(__('general.Enter document number'))
                                ->columnSpan(1),
                            
                            self::documentPhotoUpload()
                                ->columnSpan(2),
                        ]),
                ])
                ->columns(2),

            Forms\Components\Fieldset::make(__('general.Employment Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('hired_date')
                                ->label(__('general.Hired Date'))
                                ->default(now())
                                ->disabled()
                                ->helperText(__('general.Automatically set to current date'))
                                ->columnSpan(1),
                            
                            Forms\Components\TextInput::make('hired_by')
                                ->label(__('general.Hired By'))
                                ->default(\Illuminate\Support\Facades\Auth::user()?->name)
                                ->maxLength(255)
                                ->disabled()
                                ->helperText(__('general.Automatically set to current user'))
                                ->columnSpan(1),
                        ]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // الاسم الكامل مع تنسيق محسن
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('general.Full Name'))
                    ->getStateUsing(fn ($record) => trim($record->firstname . ' ' . $record->midname . ' ' . $record->lastname))
                    ->searchable(['firstname', 'midname', 'lastname'])
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->tooltip(fn ($record) => __('general.Full Name') . ': ' . trim($record->firstname . ' ' . $record->midname . ' ' . $record->lastname)),

                // البريد الإلكتروني مع أيقونة
                Tables\Columns\TextColumn::make('email')
                    ->label(__('general.Email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->copyable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->email),

                // رقم الهاتف مع تنسيق محسن
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('general.Phone'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->color('info'),

                // الحالة مع ألوان وأيقونات
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.Status'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'inactive' => 'heroicon-o-x-circle',
                        'suspended' => 'heroicon-o-exclamation-triangle',
                        'pending' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'suspended' => __('general.Suspended'),
                        'pending' => __('general.Pending'),
                        default => $state,
                    }),

                // الجنسية
                self::nationalityColumn()
                    ->toggleable(),

                // العنوان مع تقصير النص
                Tables\Columns\TextColumn::make('address')
                    ->label(__('general.Address'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->color('gray')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->address)
                    ->toggleable(isToggledHiddenByDefault: true),

                // نوع الوثيقة
                Tables\Columns\TextColumn::make('document_type')
                    ->label(__('general.Document Type'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-identification')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'passport' => __('general.Passport'),
                        'id_card' => __('general.ID Card'),
                        'driver_license' => __('general.Driver License'),
                        'residency_permit' => __('general.Residency Permit'),
                        'other' => __('general.Other'),
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // رقم الوثيقة
                Tables\Columns\TextColumn::make('document_number')
                    ->label(__('general.Document Number'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-hashtag')
                    ->color('gray')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // تاريخ الميلاد
                Tables\Columns\TextColumn::make('birth_date')
                    ->label(__('general.Birth Date'))
                    ->searchable()
                    ->sortable()
                    ->date('Y-m-d')
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // تاريخ التوظيف
                Tables\Columns\TextColumn::make('hired_date')
                    ->label(__('general.Hired Date'))
                    ->searchable()
                    ->sortable()
                    ->date('Y-m-d')
                    ->icon('heroicon-o-briefcase')
                    ->color('success')
                    ->toggleable(),

                // من قام بالتوظيف
                Tables\Columns\TextColumn::make('hired_by')
                    ->label(__('general.Hired By'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                // حالة التحقق من البريد الإلكتروني
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('general.Email Verified'))
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseColor('danger')
                    ->trueColor('success')
                    ->tooltip(function ($record) {
                        return $record->email_verified_at 
                            ? __('general.Verified on') . ' ' . $record->email_verified_at->format('Y-m-d H:i')
                            : __('general.Email not verified');
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // فلترة بالاسم الكامل
                Tables\Filters\Filter::make('full_name')
                    ->label(__('general.Full Name'))
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(__('general.Search by name'))
                            ->placeholder(__('general.Enter first, middle, or last name'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where(function ($query) use ($name) {
                                $query->where('firstname', 'like', "%{$name}%")
                                      ->orWhere('midname', 'like', "%{$name}%")
                                      ->orWhere('lastname', 'like', "%{$name}%");
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['name']) {
                            return null;
                        }
                        return __('general.Full Name') . ': ' . $data['name'];
                    }),

                // فلترة بالبريد الإلكتروني
                Tables\Filters\Filter::make('email')
                    ->label(__('general.Email'))
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label(__('general.Email'))
                            ->placeholder(__('general.Enter email address'))
                            ->email()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['email'],
                            fn (Builder $query, $email): Builder => $query->where('email', 'like', "%{$email}%")
                        );
                    }),

                // فلترة بالحالة مع تحسينات
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.Status'))
                    ->options([
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'suspended' => __('general.Suspended'),
                        'pending' => __('general.Pending'),
                    ])
                    ->multiple()
                    ->searchable(),

                // فلترة بنوع الوثيقة
                Tables\Filters\SelectFilter::make('document_type')
                    ->label(__('general.Document Type'))
                    ->options([
                        'passport' => __('general.Passport'),
                        'id_card' => __('general.ID Card'),
                        'driver_license' => __('general.Driver License'),
                        'residency_permit' => __('general.Residency Permit'),
                        'other' => __('general.Other'),
                    ])
                    ->multiple()
                    ->searchable(),

                // فلترة بالجنسية
                self::nationalityFilter(),

                // فلترة بتاريخ التوظيف
                Tables\Filters\Filter::make('hired_date')
                    ->label(__('general.Hired Date'))
                    ->form([
                        Forms\Components\DatePicker::make('hired_from')
                            ->label(__('general.From'))
                            ->placeholder(__('general.Select start date')),
                        Forms\Components\DatePicker::make('hired_until')
                            ->label(__('general.To'))
                            ->placeholder(__('general.Select end date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['hired_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('hired_date', '>=', $date),
                            )
                            ->when(
                                $data['hired_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('hired_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['hired_from'] ?? null) {
                            $indicators['hired_from'] = __('general.Hired from') . ': ' . Carbon::parse($data['hired_from'])->toFormattedDateString();
                        }
                        if ($data['hired_until'] ?? null) {
                            $indicators['hired_until'] = __('general.Hired until') . ': ' . Carbon::parse($data['hired_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),

                // فلترة بوجود الصورة الشخصية
                Tables\Filters\TernaryFilter::make('profile_photo')
                    ->label(__('general.Has Profile Photo'))
                    ->trueLabel(__('general.With Photo'))
                    ->falseLabel(__('general.Without Photo'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('profile_photo')->where('profile_photo', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('profile_photo')->orWhere('profile_photo', ''),
                    ),

                // فلترة بوجود صورة الوثيقة
                Tables\Filters\TernaryFilter::make('document_photo')
                    ->label(__('general.Has Document Photo'))
                    ->trueLabel(__('general.With Photo'))
                    ->falseLabel(__('general.Without Photo'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('document_photo')->where('document_photo', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('document_photo')->orWhere('document_photo', ''),
                    ),

                // فلترة بالعمر
                Tables\Filters\Filter::make('age')
                    ->label(__('general.Age'))
                    ->form([
                        Forms\Components\TextInput::make('min_age')
                            ->label(__('general.Minimum Age'))
                            ->numeric()
                            ->placeholder('18'),
                        Forms\Components\TextInput::make('max_age')
                            ->label(__('general.Maximum Age'))
                            ->numeric()
                            ->placeholder('65'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_age'],
                                fn (Builder $query, $age): Builder => $query->whereDate('birth_date', '<=', now()->subYears($age)),
                            )
                            ->when(
                                $data['max_age'],
                                fn (Builder $query, $age): Builder => $query->whereDate('birth_date', '>=', now()->subYears($age)),
                            );
                    }),
            ])
            ->filtersFormColumns(2)
            ->persistFiltersInSession()

            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export'))
                    ->fileName('property-managers-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('general.View')),
                Tables\Actions\EditAction::make()->label(__('general.Edit')),
                
                // تغيير الحالة
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->status === 'active' ? __('general.Deactivate') : __('general.Activate'))
                    ->icon(fn ($record) => $record->status === 'active' ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                    ->color(fn ($record) => $record->status === 'active' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->status === 'active' ? __('general.Deactivate Manager') : __('general.Activate Manager'))
                    ->modalDescription(fn ($record) => $record->status === 'active' 
                        ? __('general.Are you sure you want to deactivate this manager?')
                        : __('general.Are you sure you want to activate this manager?'))
                    ->action(function ($record) {
                        $record->update([
                            'status' => $record->status === 'active' ? 'inactive' : 'active'
                        ]);
                    })
                    ->successNotificationTitle(fn ($record) => $record->status === 'active' 
                        ? __('general.Manager activated successfully')
                        : __('general.Manager deactivated successfully')),
                
                Tables\Actions\DeleteAction::make()->label(__('general.Delete')),

                // إعادة إرسال رابط التحقق
                Tables\Actions\Action::make('resend_verification')
                    ->label(__('general.Resend Verification'))
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->sendEmailVerificationNotification();
                        Notification::make()
                            ->title(__('general.Verification email sent'))
                            ->body(__('general.Verification link sent to') . ' ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Resend Email Verification Link'))
                    ->modalDescription(__('general.Are you sure you want to resend the verification link?'))
                    ->modalSubmitActionLabel(__('general.Send')),

                    Tables\Actions\Action::make('mark_verified')
                    ->label(__('general.Mark as Verified'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->update(['email_verified_at' => now()]);
                        Notification::make()
                            ->title(__('general.Email Verified Successfully'))
                            ->body(__('general.Email verified for') . ' ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Manually Verify Email'))
                    ->modalDescription(__('general.Are you sure you want to manually verify the email?'))
                    ->modalSubmitActionLabel(__('general.Verify')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('general.Delete Selected')),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected'))
                        ->fileName('property-managers-selected')
                        ->defaultFormat('pdf')
                        ->disablePreview(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccs::route('/'),
            'create' => Pages\CreateAcc::route('/create'),
            'view' => Pages\ViewAcc::route('/{record}'),
            'edit' => Pages\EditAcc::route('/{record}/edit'),
        ];
    }

}
