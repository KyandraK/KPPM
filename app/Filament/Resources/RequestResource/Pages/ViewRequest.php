<?php

namespace App\Filament\Resources\RequestResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use App\Filament\Resources\RequestResource;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('filament.actions.print'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn() => route('requests.print', $this->record->id)),
        ];
    }
}
