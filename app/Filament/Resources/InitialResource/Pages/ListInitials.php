<?php

namespace App\Filament\Resources\InitialResource\Pages;

use App\Filament\Resources\InitialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;

class ListInitials extends ListRecords
{
    protected static string $resource = InitialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return parent::getTableQuery();
        }

        $division = $user->division;

        return parent::getTableQuery()->whereHas('request.user', function (Builder $query) use ($division) {
            $query->where('division', $division);
        });
    }


    public function getTabs(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return [
                'All' => Tab::make()
                    ->label(__('filament.tabs.all')),
                'Awaiting Approval' => Tab::make()
                    ->label(__('filament.tabs.awaiting'))
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query->where('status', 'Awaiting Approval')
                    )
                    ->badge(
                        \App\Models\Initial::query()
                            ->where('status', 'Awaiting Approval')
                            ->count()
                    ),
            ];
        }

        $division = $user->division;

        return [
            'All' => Tab::make()
                ->label(__('filament.tabs.all')),
            'Awaiting Approval' => Tab::make()
                ->label(__('filament.tabs.awaiting'))
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('status', 'Awaiting Approval')
                        ->whereHas('request.user', function (Builder $query) use ($division) {
                            $query->where('division', $division);
                        })
                )
                ->badge(
                    \App\Models\Initial::query()
                        ->where('status', 'Awaiting Approval')
                        ->whereHas('request.user', function (Builder $query) use ($division) {
                            $query->where('division', $division);
                        })
                        ->count()
                ),
        ];
    }
}
