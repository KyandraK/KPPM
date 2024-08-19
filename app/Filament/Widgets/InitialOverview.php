<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Facades\Auth;
use App\Models\Initial;

class InitialOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $approvedCount = Initial::where('status', 'Approved')->count();
            $waitingCount = Initial::where('status', 'Awaiting Approval')->count();
            $holdCount = Initial::where('status', 'On Hold')->count();
        } else {
            $division = $user->division;

            $approvedCount = Initial::where('status', 'Approved')
                ->whereHas('request.user', function ($query) use ($division) {
                    $query->where('division', $division);
                })
                ->count();

            $waitingCount = Initial::where('status', 'Awaiting Approval')
                ->whereHas('request.user', function ($query) use ($division) {
                    $query->where('division', $division);
                })
                ->count();

            $holdCount = Initial::where('status', 'On Hold')
                ->whereHas('request.user', function ($query) use ($division) {
                    $query->where('division', $division);
                })
                ->count();
        }

        return [
            Stat::make('Approved', $approvedCount)
                ->label(__('filament.widget.approval_approved'))
                ->description(__('filament.widget.approval_approved_desc'))
                ->descriptionIcon('mdi-check-circle-outline', IconPosition::Before)
                ->chart([3, 5, 8, 7, 5])
                ->color('success'),
            Stat::make('Awaiting Approval', $waitingCount)
                ->label(__('filament.widget.approval_awaiting'))
                ->description(__('filament.widget.approval_awaiting_desc'))
                ->descriptionIcon('mdi-alert-circle-outline', IconPosition::Before)
                ->chart([3, 1, 4, 2, 2])
                ->color('warning'),
            Stat::make('On Hold', $holdCount)
                ->label(__('filament.widget.approval_rejected'))
                ->description(__('filament.widget.approval_rejected_desc'))
                ->descriptionIcon('mdi-close-circle-outline', IconPosition::Before)
                ->chart([1, 2, 8, 7, 7])
                ->color('danger'),
        ];
    }
}
