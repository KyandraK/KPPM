<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Inspection;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InspectionResource\Pages;
use App\Filament\Resources\InspectionResource\RelationManagers;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    public static function getModelLabel(): string
    {
        return __('filament.panel.inspection');
    }

    protected static ?int $navigationSort = 5;

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
                            ->schema([
                                Forms\Components\DatePicker::make('inspection_date')
                                    ->label(__('filament.forms.fields.inspection_date'))
                                    ->displayFormat('d/m/Y')
                                    ->minDate(Carbon::today())
                                    ->required(),
                                Forms\Components\Select::make('model')
                                    ->label(__('filament.forms.fields.model'))
                                    ->native(false)
                                    ->options(function (callable $get) {
                                        $requestId = $get('request_id');
                                        if ($requestId) {
                                            $request = \App\Models\Request::find($requestId);
                                            if ($request) {
                                                return \App\Models\Vehicle::where('wheels', $request->wheels)
                                                    ->where('status', 'available')
                                                    ->pluck('model', 'model')
                                                    ->unique();
                                            }
                                        }
                                        return [];
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($set, $state) {
                                        $set('license_plate', null);
                                    })
                                    ->required(),
                                Forms\Components\Select::make('license_plate')
                                    ->label(__('filament.forms.fields.license_plate'))
                                    ->native(false)
                                    ->options(function (callable $get) {
                                        $model = $get('model');
                                        if ($model) {
                                            return \App\Models\Vehicle::where('model', $model)
                                                ->where('status', 'available')
                                                ->pluck('license_plate', 'license_plate');
                                        }
                                        return [];
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($set, $state, $get) {
                                        $model = $get('model');
                                        $licensePlate = $state;
                                        if ($model && $licensePlate) {
                                            $vehicle = \App\Models\Vehicle::where('model', $model)
                                                ->where('license_plate', $licensePlate)
                                                ->first();
                                            if ($vehicle) {
                                                $set('vehicle_id', $vehicle->id);
                                                $request = \App\Models\Request::find($get('request_id'));
                                                if ($request) {
                                                    $request->vehicle_id = $vehicle->id;
                                                    $request->save();
                                                }
                                            }
                                        }
                                    })
                                    ->required(),
                                Forms\Components\Hidden::make('vehicle_id'),
                                Forms\Components\TextInput::make('kilometer')
                                    ->label(__('filament.forms.fields.kilometer'))
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TimePicker::make('jam_keluar')
                                    ->label(__('filament.forms.fields.jam_keluar'))
                                    ->required(),
                                Forms\Components\Select::make('bbm')
                                    ->label(__('filament.forms.fields.bbm'))
                                    ->options([
                                        'e' => 'Empty',
                                        '1/4' => '1/4',
                                        '1/2' => '1/2',
                                        '3/4' => '3/4',
                                        'f' => 'Full',
                                    ])
                                    ->native(false)
                                    ->required(),
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
                                            ->bulkToggleable()
                                            ->columns(4)
                                            ->columnSpanFull()
                                            ->gridDirection('row'),
                                    ]),
                                Forms\Components\Textarea::make('comments')
                                    ->label(__('filament.forms.fields.comments'))
                                    ->columnSpan('full')
                                    ->required(),
                                Forms\Components\FileUpload::make('images')
                                    ->label(__('filament.forms.fields.images'))
                                    ->multiple()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpan('full'),
                            ])->columns(3),
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
                Tables\Actions\EditAction::make()
                    ->label(__('filament.actions.edit'))
                    ->color('info')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(fn($record) => $record->request->status === 'Approved'),
                Tables\Actions\Action::make('In Use')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        if ($record->request && $record->request->status === 'Ready for Pickup') {
                            $record->request->status = 'In Use';
                            $record->request->save();

                            $vehicle = \App\Models\Vehicle::where('license_plate', $record->license_plate)->first();
                            if ($vehicle) {
                                $vehicle->status = 'In Use';
                                $vehicle->save();
                            }

                            \App\Models\Post::create([
                                'request_id' => $record->request_id,
                                'inspection_id' => $record->id,
                            ]);
                        }
                    })
                    ->visible(fn($record) => $record->request->status === 'Ready for Pickup')
                    ->color('success'),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('cancel_reason')
                            ->label(__('filament.forms.fields.cancel_reason'))
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->update([
                            'status' => 'Canceled',
                            'cancel_reason' => $data['cancel_reason'],
                        ]);
                        if ($record->request) {
                            $record->request->update([
                                'status' => 'Canceled',
                                'approval_reason' => $data['cancel_reason'],
                            ]);
                            $user = $record->request->user;
                            if ($user) {
                                Notification::make()
                                    ->title(__('filament.notification.inspection_cancel_title'))
                                    ->body(__('filament.notification.inspection_cancel_body') . $data['cancel_reason'])
                                    ->sendToDatabase([$user]);
                            }
                        }
                    })
                    ->visible(fn($record) => $record->request->status === 'Approved')
                    ->requiresConfirmation(),
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
            'index' => Pages\ListInspections::route('/'),
            'create' => Pages\CreateInspection::route('/create'),
            'edit' => Pages\EditInspection::route('/{record}/edit'),
        ];
    }
}
