<?php

namespace App\Filament\Resources\HistoryResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\HistoryResource\Pages\ListHistories;

class KilometreOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListHistories::class;
    }

    protected function getStats(): array
    {
        $pageQuery = $this->getPageTableQuery();

        $histories = $pageQuery->with(['post', 'vehicle'])->get();

        $totalKilometers = $histories->groupBy('vehicle.license_plate')->sum(function ($vehicleHistories) {
            $minKilometer = $vehicleHistories->min(fn($history) => $history->post->inspection->kilometer ?? 0);
            $maxPostKilometer = $vehicleHistories->max(fn($history) => $history->post->post_kilometer ?? 0);
            return $maxPostKilometer - $minKilometer;
        });

        $totalVehicles = $pageQuery->distinct('id')->count('id');

        return [
            Stat::make(__('filament.widget.used'), $totalVehicles),
            Stat::make(__('filament.widget.traveled'), number_format($totalKilometers, 0, ',', '.') . ' km'),
        ];
    }
}
