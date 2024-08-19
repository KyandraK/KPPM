<?php

namespace App\Filament\Resources\HistoryResource\Pages;

use App\Filament\Resources\HistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\HistoryResource\Widgets\KilometreOverview;

class ListHistories extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = HistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KilometreOverview::class,
        ];
    }
}
