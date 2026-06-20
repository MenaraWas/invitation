<?php

namespace App\Filament\Resources\Guests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GuestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('token')
                    ->required(),
                Select::make('status')
                    ->options(['active' => 'Active', 'revoked' => 'Revoked'])
                    ->default('active')
                    ->required(),
                Select::make('rsvp_status')
                    ->options(['pending' => 'Pending', 'attending' => 'Attending', 'not_attending' => 'Not attending'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
