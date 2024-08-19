<?php

namespace App\Filament\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        return [
            Stat::make('User', \App\Models\User::count())
                ->label(__('filament.widget.user_user'))
                ->description(__('filament.widget.user_user_desc'))
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->chart([1, 4, 2, 3, 7])
                ->color('success'),
            Stat::make('Request', \App\Models\Request::count())
                ->label(__('filament.widget.user_request'))
                ->description(__('filament.widget.user_request_desc'))
                ->descriptionIcon('mdi-file-document-multiple-outline', IconPosition::Before)
                ->chart([3, 4, 1, 3, 2])
                ->color('info'),
            Stat::make('Vehicle', \App\Models\Vehicle::count())
                ->label(__('filament.widget.user_vehicle'))
                ->description(__('filament.widget.user_vehicle_desc'))
                ->descriptionIcon('mdi-car-multiple', IconPosition::Before)
                ->chart([3, 1, 4, 2, 2])
                ->color('gray'),
        ];
    }
}
