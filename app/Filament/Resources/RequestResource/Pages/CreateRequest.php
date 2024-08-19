<?php

namespace App\Filament\Resources\RequestResource\Pages;

use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RequestResource;
use Spatie\Permission\Models\Role;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function getRedirectUrl(): string
    {
        $name = Auth::user()->name;
        $userDivision = Auth::user()->division;

        $kepalaUsers = Role::where('name', 'kepala')
            ->first()
            ->users()
            ->where('division', $userDivision)
            ->get();

        foreach ($kepalaUsers as $kepalaUser) {
            Notification::make()
                ->title(__('filament.notification.request') . ' ' . $name)
                ->body(__('filament.notification.request_body'))
                ->sendToDatabase($kepalaUser);
        }

        return $this->getResource()::getUrl('index');
    }
}
