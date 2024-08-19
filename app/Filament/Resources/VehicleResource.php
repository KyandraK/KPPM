<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'mdi-car-cog';

    public static function getPluralModelLabel(): string
    {
        return __('filament.panel.vehicle');
    }

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('make')
                            ->label(__('filament.forms.fields.make'))
                            ->required(),
                        Forms\Components\TextInput::make('model')
                            ->label(__('filament.forms.fields.model'))
                            ->required(),
                        Forms\Components\TextInput::make('year')
                            ->label(__('filament.forms.fields.year'))
                            ->required(),
                        Forms\Components\TextInput::make('vin')
                            ->label(__('filament.forms.fields.vin'))
                            ->required(),
                        Forms\Components\TextInput::make('license_plate')
                            ->label(__('filament.forms.fields.license_plate'))
                            ->required(),
                        Forms\Components\Select::make('wheels')
                            ->label(__('filament.forms.fields.wheels'))
                            ->required()
                            ->options([
                                '6' => '6',
                                '4' => '4',
                                '3' => '3',
                                '2' => '2',
                                'Patroli' => 'Patroli',
                            ])
                            ->native(false),
                        Forms\Components\FileUpload::make('image')
                            ->label(__('filament.forms.fields.image'))
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->openable()
                            ->downloadable()
                            ->columnSpan('full'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wheels')
                    ->label(__('filament.tables.columns.wheels')),
                Tables\Columns\TextColumn::make('model')
                    ->label(__('filament.tables.columns.model'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_plate')
                    ->label(__('filament.tables.columns.license_plate'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'Available',
                        'info' => 'Ready for Pickup',
                        'gray' => 'Completed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\ViewAction::make()
                        ->color('gray'),
                    Tables\Actions\Action::make('changeStatus')
                        ->label('Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Change Status')
                                ->options([
                                    'Available' => 'Available',
                                    'In Use' => 'In Use',
                                    'Ready for Pickup' => 'Ready for Pickup',
                                ])
                                ->native(false)
                                ->required(),
                        ])
                        ->action(function (array $data, Vehicle $record): void {
                            $record->update(['status' => $data['status']]);
                        }),
                    Tables\Actions\DeleteAction::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation(),
                ])->color('gray'),
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
            RelationManagers\HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
            'view' => Pages\ViewVehicle::route('/{record}'),
        ];
    }
}
