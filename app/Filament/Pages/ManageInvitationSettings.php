<?php

use App\Models\InvitationSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageInvitationSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Pengaturan Undangan';
    protected static string $view = 'filament.pages.manage-invitation-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(InvitationSetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('content_type')
                ->options([
                    'website' => 'Website',
                    'image' => 'Gambar',
                    'pdf' => 'PDF',
                ])
                ->required(),
            Forms\Components\TextInput::make('content_url')
                ->label('URL Konten Undangan')
                ->url()
                ->required()
                ->helperText('Link website/gambar/PDF undangan yang sudah jadi.'),
        ])->statePath('data');
    }

    public function save(): void
    {
        InvitationSetting::current()->update($this->form->getState());

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }
}