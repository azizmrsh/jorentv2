<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Traits\FileUploadTrait;
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

class UserResource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $navigationLabel = null;
    protected static ?string $label = null;
    protected static ?string $pluralLabel = null;
    
    public static function getNavigationLabel(): string
    {
        return __('general.Managers');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Manager');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Managers');
    }
    protected static ?string $slug = 'managers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Fieldset::make(__('general.Personal Information'))
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('name')
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
                                ->required()
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
                            
                            Forms\Components\Select::make('role')
                                ->label(__('general.Role'))
                                ->required()
                                ->options([
                                    'admin' => __('general.Admin'),
                                    'manager' => __('general.Manager'),
                                    'user' => __('general.User'),
                                    'owner' => __('general.Owner'),
                                ])
                                ->default('user')
                                ->native(false)
                                ->placeholder(__('general.Select role'))
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
                                ->unique(table: 'users', column: 'email', ignoreRecord: true)
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
                        ->required()
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // الاسم الكامل مع تنسيق محسن
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('general.Full Name'))
                    ->getStateUsing(fn ($record) => trim($record->name . ' ' . $record->midname . ' ' . $record->lastname))
                    ->searchable(['name', 'midname', 'lastname'])
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->tooltip(fn ($record) => __('general.Full Name') . ': ' . trim($record->name . ' ' . $record->midname . ' ' . $record->lastname)),

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

                // الدور مع ألوان وأيقونات
                Tables\Columns\TextColumn::make('role')
                    ->label(__('general.Role'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'owner' => 'success',
                        'user' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-o-shield-check',
                        'manager' => 'heroicon-o-user-group',
                        'owner' => 'heroicon-o-building-office',
                        'user' => 'heroicon-o-user',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => __('general.Admin'),
                        'manager' => __('general.Manager'),
                        'owner' => __('general.Owner'),
                        'user' => __('general.User'),
                        default => $state,
                    }),

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

                // التحقق من البريد الإلكتروني
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

                // تاريخ الميلاد
                Tables\Columns\TextColumn::make('birth_date')
                    ->label(__('general.Birth Date'))
                    ->searchable()
                    ->sortable()
                    ->date('Y-m-d')
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // تاريخ الإنشاء
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                                $query->where('name', 'like', "%{$name}%")
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

                // فلترة بالدور مع تحسينات
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('general.Role'))
                    ->options([
                        'admin' => __('general.Admin'),
                        'manager' => __('general.Manager'),
                        'user' => __('general.User'),
                        'owner' => __('general.Owner'),
                    ])
                    ->multiple()
                    ->searchable(),

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

                // فلترة بالتحقق من البريد الإلكتروني
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label(__('general.Email Verified'))
                    ->trueLabel(__('general.Verified'))
                    ->falseLabel(__('general.Not Verified'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),

                // فلترة بتاريخ الميلاد
                Tables\Filters\Filter::make('birth_date')
                    ->label(__('general.Birth Date'))
                    ->form([
                        Forms\Components\DatePicker::make('birth_from')
                            ->label(__('general.From'))
                            ->placeholder(__('general.Select start date')),
                        Forms\Components\DatePicker::make('birth_until')
                            ->label(__('general.To'))
                            ->placeholder(__('general.Select end date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['birth_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('birth_date', '>=', $date),
                            )
                            ->when(
                                $data['birth_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('birth_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['birth_from'] ?? null) {
                            $indicators['birth_from'] = __('general.Birth from') . ': ' . Carbon::parse($data['birth_from'])->toFormattedDateString();
                        }
                        if ($data['birth_until'] ?? null) {
                            $indicators['birth_until'] = __('general.Birth until') . ': ' . Carbon::parse($data['birth_until'])->toFormattedDateString();
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
                    ->fileName('managers-export')
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

                // إرسال رابط التحقق
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

                // تأكيد البريد يدوياً
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
                
                Tables\Actions\DeleteAction::make()->label(__('general.Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('general.Delete Selected')),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected'))
                        ->fileName('managers-selected')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
