<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Components\Rating;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function getModelLabel(): string
    {
        return __('filament.panel.post');
    }

    protected static ?int $navigationSort = 6;

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
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->inspection_date);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('model')
                                    ->label(__('filament.forms.fields.model'))
                                    ->native(false)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->model);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('license_plate')
                                    ->label(__('filament.forms.fields.license_plate'))
                                    ->native(false)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->license_plate);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Hidden::make('vehicle_id')
                                    ->label(__('filament.forms.fields.vehicle_id'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->vehicle_id);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\TextInput::make('kilometer')
                                    ->label(__('filament.forms.fields.kilometer'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->kilometer);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\TimePicker::make('jam')
                                    ->label(__('filament.forms.fields.jam_keluar'))
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->jam_keluar);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Select::make('bbm')
                                    ->label(__('filament.forms.fields.bbm'))
                                    ->native(false)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->bbm);
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
                                                if ($record && $record->inspection) {
                                                    $component->state($record->inspection->peralatan);
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
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->comments);
                                        }
                                    })
                                    ->disabled()
                                    ->required(),
                                Forms\Components\FileUpload::make('images')
                                    ->label(__('filament.forms.fields.images'))
                                    ->multiple()
                                    ->reorderable()
                                    ->openable()
                                    ->downloadable()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->inspection) {
                                            $component->state($record->inspection->images);
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
                                    ->displayFormat('d/m/Y')
                                    ->minDate(Carbon::today())
                                    ->required(),
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
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TimePicker::make('post_jam_keluar')
                                    ->label(__('filament.forms.fields.jam_masuk'))
                                    ->required(),
                                Forms\Components\Select::make('post_bbm')
                                    ->label(__('filament.forms.fields.bbm'))
                                    ->options([
                                        'e' => 'Empty',
                                        '1/4' => '1/4',
                                        '1/2' => '1/2',
                                        '3/4' => '3/4',
                                        'f' => 'Full',
                                    ])
                                    ->native(false)
                                    ->label('BBM')
                                    ->required(),
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
                                            ->gridDirection('row'),
                                    ]),
                                Forms\Components\Textarea::make('post_comments')
                                    ->label(__('filament.forms.fields.comments'))
                                    ->columnSpan('full')
                                    ->required(),
                                Forms\Components\FileUpload::make('post_images')
                                    ->label(__('filament.forms.fields.images'))
                                    ->multiple()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpan('full'),
                            ])->columns(2),
                        Tabs\Tab::make('Rating')
                            ->visible(function ($record) {
                                return $record->rating !== null;
                            })
                            ->schema([
                                Rating::make('rating')
                                    ->label('Rating')
                                    ->size('lg')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->rating);
                                        }
                                    })
                                    ->disabled(),
                                Forms\Components\Textarea::make('feedback')
                                    ->label('Feedback')
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->feedback);
                                        }
                                    })
                                    ->disabled(),
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request.created_at')
                    ->label(__('filament.tables.columns.created_at'))
                    ->dateTime('d/m/Y (H:i:s)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request.user.name')
                    ->label(__('filament.tables.columns.user_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('request.wheels')
                    ->label(__('filament.tables.columns.wheels'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('request.status')
                    ->label(__('filament.tables.columns.status'))
                    ->badge()
                    ->colors([
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                        'rose' => 'Canceled',
                        'info' => 'Ready for Pickup',
                        'gray' => 'Completed',
                    ]),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
