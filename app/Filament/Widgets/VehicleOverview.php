<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class VehicleOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $availableCount = Vehicle::where('status', 'available')->count();
        $readyForPickupCount = Vehicle::where('status', 'ready for pickup')->count();
        $inUseCount = Vehicle::where('status', 'in use')->count();

        return [
            Stat::make('Available', $availableCount)
                ->label(__('filament.widget.vehicle_available'))
                ->description(__('filament.widget.vehicle_available_desc'))
                ->descriptionIcon('mdi-car', IconPosition::Before)
                ->chart([3, 5, 8, 7, 5])
                ->color('success'),
            Stat::make('Ready for Pickup', $readyForPickupCount)
                ->label(__('filament.widget.vehicle_ready'))
                ->description(__('filament.widget.vehicle_ready_desc'))
                ->descriptionIcon('mdi-car-key', IconPosition::Before)
                ->chart([3, 1, 5, 2, 6])
                ->color('info'),
            Stat::make('In Use', $inUseCount)
                ->label(__('filament.widget.vehicle_use'))
                ->description(__('filament.widget.vehicle_use_desc'))
                ->descriptionIcon('mdi-car-off', IconPosition::Before)
                ->chart([2, 5, 3, 4, 7])
                ->color('warning'),
        ];
    }
}
