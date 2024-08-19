<?php

namespace App\Filament\Widgets;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ApprovalOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $approvedCount = \App\Models\Approval::whereIn('status', ['Completed', 'Approved'])->count();
        $waitingCount = \App\Models\Approval::where('status', 'Awaiting Approval')->count();
        $holdCount = \App\Models\Approval::where('status', 'On Hold')->count();

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
