<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->label(__('filament.tabs.all')),
            '2' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('wheels', '2')),
            '3' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('wheels', '3')),
            '4' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('wheels', '4')),
            'Patroli' => Tab::make()
                ->label(__('filament.tabs.patrol'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('wheels', 'Patroli')),
        ];
    }
}
