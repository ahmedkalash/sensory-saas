<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected string $view = 'filament.pages.profile';

    // Set to true to show the page in the navigation sidebar
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $title = 'الملف الشخصي';

    protected static ?int $navigationSort = 99;

    // Add title for the navigation bar
    protected static ?string $navigationLabel = 'الملف الشخصي';


    public ?array $data = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $this->form->fill($user->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('الملف الشخصي')
                    ->description('إدارة معلومات حسابك وكلمة المرور')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique('users', 'email', auth()->user()),
                    ])
                    ->columns(),
                Section::make('كلمة المرور')
                    ->description('تغيير كلمة المرور الخاصة بك')
                    ->schema([
                        TextInput::make('new_password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->rule(Password::default())
                            ->dehydrated(false)
                            ->autocomplete('new-password'),
                        TextInput::make('new_password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password')
                            ->dehydrated(false)
                            ->autocomplete('new-password'),
                    ])
                    ->columns(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Password change logic (no current password needed)

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['new_password'])) {
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);
        }

        Notification::make()
            ->title('تم حفظ التغييرات بنجاح')
            ->success()
            ->send();

        $this->form->fill($user->attributesToArray());
    }
}
