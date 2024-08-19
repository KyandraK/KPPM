<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Approval;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

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
            'Awaiting Approval' => Tab::make()
                ->label(__('filament.tabs.awaiting'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'Awaiting Approval'))
                ->badge(Approval::query()->where('status', 'Awaiting Approval')->count()),
        ];
    }
}
