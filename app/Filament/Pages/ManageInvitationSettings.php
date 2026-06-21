<?php

namespace App\Filament\Pages;

use App\Models\InvitationSetting;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class ManageInvitationSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;
    protected static ?string $navigationLabel = 'Pengaturan Undangan';
    protected string $view = 'filament.pages.manage-invitation-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(InvitationSetting::current()->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form->components([
            DatePicker::make('event_date')
                ->label('Tanggal Acara')
                ->required()
                ->helperText('Undangan tidak bisa diakses 1 hari setelah tanggal'),

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