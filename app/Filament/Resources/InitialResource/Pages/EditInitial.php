<?php

namespace App\Filament\Resources\InitialResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\InitialResource;
use Spatie\Permission\Models\Role;


class EditInitial extends EditRecord
{
    protected static string $resource = InitialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $name = Auth::user()->name;

        $kepalaDivUsers = Role::where('name', 'kepala_div')
            ->first()
            ->users()
            ->get();

        foreach ($kepalaDivUsers as $kepalaDivUser) {
            Notification::make()
                ->title(__('filament.notification.initial') . ' ' . $name)
                ->body(__('filament.notification.initial_body'))
                ->sendToDatabase($kepalaDivUser);
        }
    }
}
