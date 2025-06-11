<?php

namespace App\Filament\Tenant\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static string $view = 'filament.tenant.pages.profile';
    
    protected static ?string $navigationLabel = 'الملف الشخصي';
    
    public ?array $data = [];

    public function mount(): void
    {
        $tenant = Auth::guard('tenant')->user();
        $this->form->fill([
            'firstname' => $tenant->firstname,
            'midname' => $tenant->midname,
            'lastname' => $tenant->lastname,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'address' => $tenant->address,
            'birth_date' => $tenant->birth_date,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الشخصية')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('firstname')
                                    ->label('الاسم الأول')
                                    ->required(),
                                Forms\Components\TextInput::make('midname')
                                    ->label('اسم الأب'),
                                Forms\Components\TextInput::make('lastname')
                                    ->label('اسم العائلة')
                                    ->required(),
                            ]),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel(),
                            ]),
                        
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->rows(3),
                            
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('تاريخ الميلاد'),
                    ]),
                    
                Forms\Components\Section::make('تغيير كلمة المرور')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->currentPassword('tenant'),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('new_password')
                                    ->label('كلمة المرور الجديدة')
                                    ->password()
                                    ->rule(Password::default())
                                    ->same('new_password_confirmation'),
                                Forms\Components\TextInput::make('new_password_confirmation')
                                    ->label('تأكيد كلمة المرور الجديدة')
                                    ->password(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $tenant = Auth::guard('tenant')->user();
        
        // Update profile data
        $profileData = collect($data)->except(['current_password', 'new_password', 'new_password_confirmation'])->toArray();
        $tenant->update($profileData);
        
        // Update password if provided
        if (!empty($data['new_password'])) {
            $tenant->update(['password' => Hash::make($data['new_password'])]);
        }

        Notification::make()
            ->title('تم حفظ الملف الشخصي بنجاح')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ التغييرات')
                ->submit('save'),
        ];
    }

    public function getTitle(): string
    {
        return 'الملف الشخصي';
    }
}
