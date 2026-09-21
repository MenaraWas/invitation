<?php

namespace App\Filament\Resources\Guests;

use App\Filament\Resources\Guests\Pages\CreateGuest;
use App\Filament\Resources\Guests\Pages\EditGuest;
use App\Filament\Resources\Guests\Pages\ListGuests;
use App\Jobs\SendWhatsAppWebInvitationJob;
use App\Models\Guest;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Notifications\Notification;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Tamu Undangan';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('phone')
                ->tel()
                ->maxLength(20),

            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'revoked' => 'Revoked',
                ])
                ->default('active')
                ->required(),

            Select::make('rsvp_status')
                ->options([
                    'pending' => 'Pending',
                    'attending' => 'Hadir',
                    'not_attending' => 'Tidak Hadir',
                ])
                ->default('pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('phone'),
            Tables\Columns\TextColumn::make('token')
                ->copyable()
                ->limit(15),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'active' => 'success',
                    'revoked' => 'danger',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('rsvp_status')->badge(),
            Tables\Columns\TextColumn::make('whatsapp_status')
                ->label('Status WA')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'queued' => 'warning',
                    'sent' => 'success',
                    'failed' => 'danger',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('device_status')
                ->label('Akses Device')
                ->state(function (Guest $record) {
                    return $record->deviceSession ? 'Sudah diakses' : 'Belum diakses';
                })
                ->badge()
                ->color(fn(Guest $record) => $record->deviceSession ? 'success' : 'gray'),
            Tables\Columns\TextColumn::make('deviceSession.last_accessed_at')
                ->label('Terakhir Akses')
                ->dateTime('d M Y H:i')
                ->placeholder('-'),
        ])
            ->actions([
                Action::make('send_whatsapp')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->action(function (Guest $record) {
                        $record->update(['whatsapp_status' => 'queued']);
                        SendWhatsAppWebInvitationJob::dispatch($record->id);

                        Notification::make()
                            ->title('Pesan WA masuk antrian')
                            ->body("Undangan untuk {$record->name} sedang dikirim.")
                            ->success()
                            ->send();
                    }),
                Action::make('reset_access')
                    ->label('Reset Akses')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Ini akan menghapus device yang terkunci dan generate link baru. Lanjutkan?')
                    ->visible(fn(Guest $record) => $record->deviceSession !== null)
                    ->action(function (Guest $record) {
                        $record->deviceSession()->delete();
                        $record->update(['token' => Guest::generateToken()]);

                        Notification::make()
                            ->title('Akses berhasil direset')
                            ->body('Token baru: ' . $record->token)
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])

            ->headerActions([
                Action::make('login_whatsapp')
                    ->label('Login WA')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('info')
                    ->url(route('wa.status'))
                    ->openUrlInNewTab(),
                Action::make('send_whatsapp_batch')
                    ->label('Kirim WA Batch')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Ini akan mengirimkan undangan ke semua tamu aktif yang masih punya nomor WA. Lanjutkan?')
                    ->action(function () {
                        $guests = Guest::query()->where('status', 'active')->whereNotNull('phone')->where('phone', '!=', '')->get();

                        foreach ($guests as $guest) {
                            $guest->update(['whatsapp_status' => 'queued']);
                            SendWhatsAppWebInvitationJob::dispatch($guest->id);
                        }

                        Notification::make()
                            ->title('Batch WA masuk antrian')
                            ->body('Dikirim ke ' . $guests->count() . ' tamu aktif.')
                            ->success()
                            ->send();
                    }),
                Action::make('bulk_import')
                    ->label('Import Tamu')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Textarea::make('data')
                            ->label('Daftar Tamu (format: Nama,Nomor WA — 1 baris per tamu)')
                            ->placeholder("Budi Santoso,628123456789\nSiti Aminah,628987654321")
                            ->rows(10)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $lines = explode("\n", trim($data['data']));
                        $count = 0;

                        foreach ($lines as $line) {
                            $parts = explode(',', trim($line));
                            if (count($parts) < 1 || empty(trim($parts[0]))) continue;

                            Guest::create([
                                'name' => trim($parts[0]),
                                'phone' => trim($parts[1] ?? ''),
                                'status' => 'active',
                                'whatsapp_status' => 'pending',
                            ]);
                            $count++;
                        }

                        Notification::make()
                            ->title("$count tamu berhasil ditambahkan")
                            ->success()
                            ->send();
                    }),
            ]);
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
            'index' => ListGuests::route('/'),
            'create' => CreateGuest::route('/create'),
            'edit' => EditGuest::route('/{record}/edit'),
        ];
    }
}
