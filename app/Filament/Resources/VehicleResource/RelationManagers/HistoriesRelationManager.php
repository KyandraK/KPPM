<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Components\Rating;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Carbon\Carbon;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('request_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament.tables.heading.histories'))
            ->recordTitleAttribute('request_id')
            ->columns([
                Tables\Columns\TextColumn::make('request.user.name')
                    ->label(__('filament.tables.columns.user_name')),
                Tables\Columns\TextColumn::make('request.inspection.kilometer')
                    ->label(__('filament.tables.columns.km_sebelum')),
                Tables\Columns\TextColumn::make('post.post_kilometer')
                    ->label(__('filament.tables.columns.km_sesudah')),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('filament.forms.fields.from'))
                            ->maxDate(now()),
                        DatePicker::make('created_until')
                            ->label(__('filament.forms.fields.until'))
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (!empty($data['created_from'])) {
                            $indicators[] = Indicator::make(__('filament.forms.fields.from') . ': ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }
                        if (!empty($data['created_until'])) {
                            $indicators[] = Indicator::make(__('filament.forms.fields.until') . ': ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('count')
                    ->hiddenLabel('Count of Records')
                    ->icon('mdi-car-clock')
                    ->modalHeading('Total Records')
                    ->color('gray')
                    ->modalCancelAction(false)
                    ->modalSubmitAction(false)
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('count')
                                    ->label(__('filament.forms.fields.used'))
                                    ->content(function () {
                                        return $this->getFilteredTableQuery()->count();
                                    }),
                                Forms\Components\Placeholder::make('total_kilometers')
                                    ->label(__('filament.forms.fields.traveled'))
                                    ->content(function () {
                                        $query = $this->getFilteredTableQuery();

                                        $postIds = $query->pluck('post_id')->toArray();

                                        $minKilometer = \App\Models\Post::whereIn('id', $postIds)
                                            ->whereHas('inspection')
                                            ->with('inspection')
                                            ->get()
                                            ->min(fn($post) => $post->inspection->kilometer);

                                        $maxKilometer = \App\Models\Post::whereIn('id', $postIds)->max('post_kilometer');

                                        return $maxKilometer - $minKilometer;
                                    }),

                                Rating::make('rating_pemohon')
                                    ->label(__('filament.forms.fields.pemohon'))
                                    ->size('xl')
                                    ->default(function () {
                                        $query = $this->getFilteredTableQuery();
                                        $requestIds = $query->pluck('request_id')->toArray();
                                        $averageRating = \App\Models\Request::whereIn('id', $requestIds)->avg('rating');
                                        return round($averageRating ?? 1);
                                    })
                                    ->disabled(),
                                Rating::make('rating_petugas')
                                    ->label(__('filament.forms.fields.petugas'))
                                    ->size('xl')
                                    ->default(function () {
                                        $query = $this->getFilteredTableQuery();
                                        $postIds = $query->pluck('post_id')->toArray();
                                        $averageRating = \App\Models\Post::whereIn('id', $postIds)->avg('rating');
                                        return round($averageRating ?? 1);
                                    })
                                    ->disabled(),
                            ]),
                    ]),
                Tables\Actions\Action::make('export')
                    ->label(__('filament.tables.heading.export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        DatePicker::make('from_date')
                            ->label(__('filament.forms.fields.from'))
                            ->maxDate(now()),
                        DatePicker::make('until_date')
                            ->label(__('filament.forms.fields.until'))
                            ->maxDate(now()),
                    ])
                    ->action(function (array $data) {
                        $fromDate = $data['from_date'];
                        $untilDate = $data['until_date'];

                        $url = route('vehicle.pdf.export', [
                            'id' => $this->getOwnerRecord()->id,
                            'from_date' => $fromDate,
                            'until_date' => $untilDate,
                        ]);

                        return redirect($url);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
