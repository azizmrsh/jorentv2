<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use App\Traits\FileUploadTrait;
use App\Traits\HasNationalityField;
use App\Helpers\NationalityHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Notifications\Notification;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TenantResource extends Resource
{
    use FileUploadTrait, HasNationalityField;
    
    protected static ?string $model = Tenant::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $slug = 'tenants';

    public static function getNavigationLabel(): string
    {
        return __('general.Tenants');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Tenant');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Tenants');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Personal Information
            Forms\Components\Fieldset::make(__('general.Personal Information'))
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('firstname')
                                ->label(__('general.First Name'))
                                ->required()
                                ->maxLength(255)
                                ->placeholder(__('general.Enter first name')),
                            
                            Forms\Components\TextInput::make('midname')
                                ->label(__('general.Middle Name'))
                                ->maxLength(255)
                                ->placeholder(__('general.Enter middle name (optional)')),
                            
                            Forms\Components\TextInput::make('lastname')
                                ->label(__('general.Last Name'))
                                ->required()
                                ->maxLength(255)
                                ->placeholder(__('general.Enter last name')),
                        ]),
                    
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\DatePicker::make('birth_date')
                                ->label(__('general.Birth Date'))
                                ->required()
                                ->maxDate(now()->subYears(18))
                                ->placeholder(__('general.Select birth date'))
                                ->helperText(__('general.You must be at least 18 years old')),
                            
                            self::nationalityField(),
                            
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
                                ->placeholder(__('general.Select status')),
                            self::profilePhotoUpload()
                                ->columnSpan(3),
                        ]),
                ])
                ->columns(1),

            // Contact Information
            Forms\Components\Fieldset::make(__('general.Contact Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->label(__('general.Email'))
                                ->email()
                                ->maxLength(255)
                                ->required()
                                ->unique(table: 'tenants', column: 'email', ignoreRecord: true)
                                ->placeholder(__('general.Enter email address')),

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
                                ->helperText(fn (string $context): string => $context === 'edit' ? __('general.Leave empty to keep current password') : ''),
                            
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
                                ->helperText(fn (string $context): string => $context === 'edit' ? __('general.Leave empty to keep current password') : ''),
                        ]),
                    
                    Forms\Components\TextInput::make('address')
                        ->label(__('general.Address'))
                        ->placeholder(__('general.Enter full address'))
                        ->maxLength(255)
                        ->required()
                        ->columnSpan(2),
                ])
                ->columns(1),

            // Document Information
            Forms\Components\Fieldset::make(__('general.Document Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('document_type')
                                ->label(__('general.Document Type'))
                                ->options([
                                    'passport' => __('general.Passport'),
                                    'id_card' => __('general.ID Card'),
                                    'driver_license' => __('general.Driver License'),
                                    'residency_permit' => __('general.Residency Permit'),
                                    'other' => __('general.Other'),
                                ])
                                ->default('passport')
                                ->native(false)
                                ->placeholder(__('general.Select document type')),
                            
                            Forms\Components\TextInput::make('document_number')
                                ->label(__('general.Document Number'))
                                ->maxLength(255)
                                ->placeholder(__('general.Enter document number')),
                        ]),
                    
                    self::documentPhotoUpload()
                        ->columnSpan(2),
                ])
                ->columns(1),

            // Employment Information
            Forms\Components\Fieldset::make(__('general.Employment Information'))
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('hired_date')
                                ->label(__('general.Hired Date'))
                                ->default(now())
                                ->placeholder(__('general.Select hired date')),
                            
                            Forms\Components\TextInput::make('hired_by')
                                ->label(__('general.Hired By'))
                                ->default(optional(Auth::user())->name)
                                ->maxLength(255)
                                ->placeholder(__('general.Enter hiring manager name')),
                        ]),
                        
                    
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Full name with enhanced formatting
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

                // Email with icon
                Tables\Columns\TextColumn::make('email')
                    ->label(__('general.Email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->copyable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->email),

                // Phone with enhanced formatting
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('general.Phone'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->color('info'),

                // Nationality with icon
                Tables\Columns\TextColumn::make('nationality')
                    ->label(__('general.Nationality'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-flag')
                    ->formatStateUsing(fn (string $state): string => NationalityHelper::getNationalityLabel($state)),

                // Status with colors and icons
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

                // Email verification
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

                // Document type
                Tables\Columns\TextColumn::make('document_type')
                    ->label(__('general.Document Type'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
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

                // Created at
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Full name filter
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
                    }),

                // Nationality filter
                self::nationalityFilter(),

                // Status filter
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

                // Email verification filter
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label(__('general.Email Verified'))
                    ->trueLabel(__('general.Verified'))
                    ->falseLabel(__('general.Not Verified'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export'))
                    ->fileName('tenants-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('general.View')),
                Tables\Actions\EditAction::make()->label(__('general.Edit')),
                
                // Toggle status
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->status === 'active' ? __('general.Deactivate') : __('general.Activate'))
                    ->icon(fn ($record) => $record->status === 'active' ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                    ->color(fn ($record) => $record->status === 'active' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => $record->status === 'active' ? 'inactive' : 'active'
                        ]);
                    }),

                Tables\Actions\DeleteAction::make()->label(__('general.Delete')),

                // Resend verification email
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

                // Mark as verified
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
                    
                    // Bulk activate
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label(__('general.Activate Selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'active']);
                            });
                            Notification::make()
                                ->title(__('general.Tenants activated successfully'))
                                ->success()
                                ->send();
                        }),

                    // Bulk deactivate
                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label(__('general.Deactivate Selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'inactive']);
                            });
                            Notification::make()
                                ->title(__('general.Tenants deactivated successfully'))
                                ->success()
                                ->send();
                        }),

                    // Export selected
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected'))
                        ->fileName('tenants-selected')
                        ->defaultFormat('pdf')
                        ->disablePreview(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
