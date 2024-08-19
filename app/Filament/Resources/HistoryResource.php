<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\History;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Components\Rating;
use App\Filament\Resources\HistoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HistoryResource\RelationManagers;

class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static ?string $navigationIcon = 'mdi-history';

    public static function getNavigationParentItem(): ?string
    {
        return __('filament.panel.vehicle');
    }

    public static function getModelLabel(): string
    {
        return __('filament.panel.histories');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Data')
                            ->schema([
                                Forms\Components\TextInput::make('user_name')
                                    ->label(__('filament.forms.fields.user_id'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->request && $record->request->user) {
                                            $component->state($record->request->user->name);
                                        }
                                    })
                                    ->columnSpanFull()
                                    ->disabled(),
                                Forms\Components\Select::make('request_id')
                                    ->relationship('request', 'wheels')
                                    ->label(__('filament.forms.fields.wheels'))
                                    ->disabled(),
                                Forms\Components\DateTimePicker::make('request_departure_time')
                                    ->label(__('filament.forms.fields.departure_time'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->request) {
                                            $component->state($record->request->departure_time);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\DateTimePicker::make('request_return_time')
                                    ->label(__('filament.forms.fields.return_time'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->request) {
                                            $component->state($record->request->return_time);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Textarea::make('reason')
                                    ->label(__('filament.forms.fields.reason'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->request) {
                                            $component->state($record->request->reason);
                                        }
                                    })
                                    ->columnSpanFull()
                                    ->disabled(),
                                Forms\Components\Select::make('request_id')
                                    ->relationship('request', 'status')
                                    ->label(__('filament.forms.fields.status'))
                                    ->columnSpanFull()
                                    ->disabled(),
                            ])->columns(3),
                        Tabs\Tab::make('Pre')
                            ->label(__('filament.tabs.pre'))
                            ->schema([
                                Forms\Components\DatePicker::make('inspection_dates')
                                    ->label(__('filament.forms.fields.inspection_date'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->inspection_date);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('model')
                                    ->label(__('filament.forms.fields.model'))
                                    ->native(false)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->model);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('license_plate')
                                    ->label(__('filament.forms.fields.license_plate'))
                                    ->native(false)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->license_plate);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Hidden::make('vehicle_id')
                                    ->label(__('filament.forms.fields.vehicle_id'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->vehicle_id);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\TextInput::make('kilometer')
                                    ->label(__('filament.forms.fields.kilometer'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->kilometer);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\TimePicker::make('jam')
                                    ->label(__('filament.forms.fields.jam_keluar'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->jam_keluar);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('bbm')
                                    ->label(__('filament.forms.fields.bbm'))
                                    ->native(false)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->bbm);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\CheckboxList::make('peralatan')
                                            ->label(__('filament.forms.fields.peralatan'))
                                            ->options([
                                                'kunci' => __('filament.forms.peralatan.kunci'),
                                                'depan' => __('filament.forms.peralatan.depan'),
                                                'tengah' => __('filament.forms.peralatan.tengah'),
                                                'belakang' => __('filament.forms.peralatan.belakang'),
                                                'p3k' => __('filament.forms.peralatan.p3k'),
                                                'segitiga' => __('filament.forms.peralatan.segitiga'),
                                                'kabel' => __('filament.forms.peralatan.kabel'),
                                                'setir' => __('filament.forms.peralatan.setir'),
                                                'ban' => __('filament.forms.peralatan.ban'),
                                                'dongkrak' => __('filament.forms.peralatan.dongkrak'),
                                            ])
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->post && $record->post->inspection) {
                                                    $component->state($record->post->inspection->peralatan);
                                                }
                                            })
                                            ->disabled()
                                            ->bulkToggleable()
                                            ->columns(4)
                                            ->columnSpanFull()
                                            ->gridDirection('row'),
                                    ]),
                                Forms\Components\Textarea::make('comments')
                                    ->label(__('filament.forms.fields.comments'))
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->comments);
                                        }
                                    })
                                    ->disabled()
                                    ->required(),
                                Forms\Components\FileUpload::make('images')
                                    ->label(__('filament.forms.fields.images'))
                                    ->multiple()
                                    ->reorderable()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post && $record->post->inspection) {
                                            $component->state($record->post->inspection->images);
                                        }
                                    })
                                    ->disabled()
                                    ->columnSpan('full'),
                            ])->columns(3),
                        Tabs\Tab::make('Post')
                            ->label(__('filament.tabs.post'))
                            ->schema([
                                Forms\Components\DatePicker::make('post_inspection_date')
                                    ->label(__('filament.forms.fields.inspection_date'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post) {
                                            $component->state($record->post->post_inspection_date);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Hidden::make('post_model')
                                    ->label(__('filament.forms.fields.model'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->model);
                                        }
                                    }),
                                Forms\Components\Hidden::make('post_license_plate')
                                    ->label(__('filament.forms.fields.license_plate'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->license_plate);
                                        }
                                    }),
                                Forms\Components\Hidden::make('vehicle_id')
                                    ->label(__('filament.forms.fields.vehicle_id'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->vehicle_id);
                                        }
                                    }),
                                Forms\Components\TextInput::make('post_kilometer')
                                    ->label(__('filament.forms.fields.kilometer'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post) {
                                            $component->state($record->post->post_kilometer);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\TimePicker::make('post_jam_keluar')
                                    ->label(__('filament.forms.fields.jam_masuk'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post) {
                                            $component->state($record->post->post_jam_keluar);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('post_bbm')
                                    ->label(__('filament.forms.fields.bbm'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post) {
                                            $component->state($record->post->post_bbm);
                                        }
                                    })
                                    ->disabled()
                                    ->options([
                                        'e' => 'Empty',
                                        '1/4' => '1/4',
                                        '1/2' => '1/2',
                                        '3/4' => '3/4',
                                        'f' => 'Full',
                                    ]),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\CheckboxList::make('post_peralatan')
                                            ->label(__('filament.forms.fields.peralatan'))
                                            ->options([
                                                'kunci' => __('filament.forms.peralatan.kunci'),
                                                'depan' => __('filament.forms.peralatan.depan'),
                                                'tengah' => __('filament.forms.peralatan.tengah'),
                                                'belakang' => __('filament.forms.peralatan.belakang'),
                                                'p3k' => __('filament.forms.peralatan.p3k'),
                                                'segitiga' => __('filament.forms.peralatan.segitiga'),
                                                'kabel' => __('filament.forms.peralatan.kabel'),
                                                'setir' => __('filament.forms.peralatan.setir'),
                                                'ban' => __('filament.forms.peralatan.ban'),
                                                'dongkrak' => __('filament.forms.peralatan.dongkrak'),
                                            ])
                                            ->bulkToggleable()
                                            ->columns(4)
                                            ->columnSpanFull()
                                            ->gridDirection('row')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->post) {
                                                    $component->state($record->post->post_peralatan);
                                                }
                                            })
                                            ->disabled(),
                                    ]),
                                Forms\Components\Textarea::make('post_comments')
                                    ->label(__('filament.forms.fields.comments'))
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post) {
                                            $component->state($record->post->post_comments);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\FileUpload::make('post_images')
                                    ->label(__('filament.forms.fields.images'))
                                    ->multiple()
                                    ->reorderable()
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->post) {
                                            $component->state($record->post->post_images);
                                        }
                                    })
                                    ->disabled(),
                            ])->columns(2),
                        Tabs\Tab::make('Rating')
                            ->schema([
                                Fieldset::make('Pemohon')
                                    ->label(__('filament.tabs.pemohon'))
                                    ->schema([
                                        Rating::make('rating_pemohon')
                                            ->label('Rating')
                                            ->size('xl')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->request) {
                                                    $component->state($record->request->rating ?? 1);
                                                } else {
                                                    $component->state(1);
                                                }
                                            })
                                            ->disabled(),
                                        Forms\Components\Textarea::make('feedback_pemohon')
                                            ->label('Feedback')
                                            ->columnSpan('full')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->request) {
                                                    $component->state($record->request->feedback);
                                                }
                                            })
                                            ->disabled(),
                                    ]),
                                Fieldset::make('Petugas')
                                    ->label(__('filament.tabs.petugas'))
                                    ->schema([
                                        Rating::make('rating')
                                            ->label('Rating')
                                            ->size('xl')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->post) {
                                                    $component->state($record->post->rating ?? 1);
                                                } else {
                                                    $component->state(1);
                                                }
                                            })
                                            ->disabled(),
                                        Forms\Components\Textarea::make('feedback')
                                            ->label('Feedback')
                                            ->columnSpan('full')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->post) {
                                                    $component->state($record->post->feedback);
                                                }
                                            })
                                            ->disabled(),
                                    ]),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.tables.columns.date_completed'))
                    ->dateTime('d/m/Y (H:i:s)')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('request.user.name')
                    ->label(__('filament.tables.columns.user_name'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('request.wheels')
                    ->label(__('filament.tables.columns.wheels'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('post.post_model')
                    ->label(__('filament.tables.columns.model'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('post.post_license_plate')
                    ->label(__('filament.tables.columns.post_license_plate'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('request.status')
                    ->label(__('filament.tables.columns.status'))
                    ->badge()
                    ->colors([
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                        'danger' => 'Canceled',
                        'info' => 'Ready for Pickup',
                        'gray' => 'Completed',
                    ])
                    ->toggleable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('vehicle_data')
                    ->form([
                        Select::make('wheels')
                            ->label(__('filament.forms.fields.wheels'))
                            ->native(false)
                            ->options([
                                '6' => '6',
                                '4' => '4',
                                '3' => '3',
                                '2' => '2',
                                'Patroli' => __('filament.tabs.patrol'),
                            ])
                            ->reactive()
                            ->afterStateUpdated(function ($set) {
                                $set('model', null);
                                $set('license_plate', null);
                            }),
                        Select::make('model')
                            ->label(__('filament.forms.fields.model'))
                            ->native(false)
                            ->options(function (callable $get) {
                                $wheels = $get('wheels');
                                if ($wheels) {
                                    return \App\Models\Vehicle::where('wheels', $wheels)
                                        ->pluck('model', 'model')
                                        ->unique();
                                }
                                return [];
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($set) {
                                $set('license_plate', null);
                            }),
                        Select::make('license_plate')
                            ->label(__('filament.forms.fields.license_plate'))
                            ->native(false)
                            ->options(function (callable $get) {
                                $model = $get('model');
                                if ($model) {
                                    return \App\Models\Vehicle::where('model', $model)
                                        ->pluck('license_plate', 'license_plate');
                                }
                                return [];
                            })
                            ->reactive(),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['wheels'])) {
                            $query->whereHas('vehicle', function ($query) use ($data) {
                                $query->where('wheels', $data['wheels']);
                            });
                        }
                        if (!empty($data['model'])) {
                            $query->whereHas('vehicle', function ($query) use ($data) {
                                $query->where('model', $data['model']);
                            });
                        }
                        if (!empty($data['license_plate'])) {
                            $query->whereHas('vehicle', function ($query) use ($data) {
                                $query->where('license_plate', $data['license_plate']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (!empty($data['wheels'])) {
                            $indicators[] = Indicator::make(__('filament.forms.fields.wheels') . ': ' . $data['wheels'])
                                ->removeField('wheels');
                        }
                        if (!empty($data['model'])) {
                            $indicators[] = Indicator::make(__('filament.forms.fields.model') . ': ' . $data['model'])
                                ->removeField('model');
                        }
                        if (!empty($data['license_plate'])) {
                            $indicators[] = Indicator::make(__('filament.forms.fields.license_plate') . ': ' . $data['license_plate'])
                                ->removeField('license_plate');
                        }
                        return $indicators;
                    }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('filament.forms.fields.from'))
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                        DatePicker::make('created_until')
                            ->label(__('filament.forms.fields.until'))
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn($query, $date) => $query->whereDate('created_at', '<=', $date)
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
            ->actions([
                //
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('export')
                        ->label('Excel')
                        ->url(fn() => url('/export') . '?' . http_build_query(request()->query())),
                    Action::make('pdf')
                        ->label('PDF')
                        ->url(fn() => route('history.export.pdf') . '?' . http_build_query(request()->query())),
                ])
                    ->label(__('filament.tables.heading.export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistories::route('/'),
            'create' => Pages\CreateHistory::route('/create'),
            'edit' => Pages\EditHistory::route('/{record}/edit'),
            'view' => Pages\ViewHistory::route('/{record}'),
        ];
    }
}
