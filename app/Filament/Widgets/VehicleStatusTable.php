<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\VehicleResource;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class VehicleStatusTable extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VehicleResource::getEloquentQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('wheels')
                    ->label(__('filament.tables.columns.wheels')),
                Tables\Columns\TextColumn::make('model')
                    ->label(__('filament.tables.columns.model'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_plate')
                    ->label(__('filament.tables.columns.license_plate'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'Available',
                        'info' => 'Ready for Pickup',
                        'gray' => 'Completed',
                    ]),
            ]);
    }
}
