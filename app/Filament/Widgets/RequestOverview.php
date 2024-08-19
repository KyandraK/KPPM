<?php

namespace App\Filament\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class RequestOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $approvedCount = \App\Models\Request::whereIn('status', ['Completed', 'Approved'])->count();
            $completedCount = \App\Models\Request::where('status', 'Completed')->count();
            $holdCount = \App\Models\Request::where('status', 'On Hold')->count();
        } else {
            $approvedCount = \App\Models\Request::where('user_id', $user->id)
                ->whereIn('status', ['Completed', 'Approved'])
                ->count();
            $completedCount = \App\Models\Request::where('user_id', $user->id)
                ->where('status', 'Completed')
                ->count();
            $holdCount = \App\Models\Request::where('user_id', $user->id)
                ->where('status', 'On Hold')
                ->count();
        }

        return [
            Stat::make('Approved', $approvedCount)
                ->label(__('filament.widget.approval_approved'))
                ->description(__('filament.widget.approval_approved_desc'))
                ->descriptionIcon('mdi-check-circle-outline', IconPosition::Before)
                ->chart([3, 5, 8, 7, 5])
                ->color('success'),
            Stat::make('Completed', $completedCount)
                ->label(__('filament.widget.approval_completed'))
                ->description(__('filament.widget.approval_completed_desc'))
                ->descriptionIcon('mdi-checkbox-marked-circle-outline', IconPosition::Before)
                ->chart([3, 1, 4, 2, 2])
                ->color('gray'),
            Stat::make('On Hold', $holdCount)
                ->label(__('filament.widget.approval_rejected'))
                ->description(__('filament.widget.approval_rejected'))
                ->descriptionIcon('mdi-close-circle-outline', IconPosition::Before)
                ->chart([1, 2, 8, 7, 7])
                ->color('danger'),
        ];
    }
}
