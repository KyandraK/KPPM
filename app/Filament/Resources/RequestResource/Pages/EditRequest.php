<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequest extends EditRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('print')
                ->label(__('filament.actions.print'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn() => route('requests.print', $this->record->id)),
        ];
    }
}
