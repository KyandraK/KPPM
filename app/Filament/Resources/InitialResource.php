<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InitialResource\Pages;
use App\Models\Initial;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InitialResource extends Resource
{
    protected static ?string $model = Initial::class;

    protected static ?string $navigationIcon = 'mdi-file-document-arrow-right-outline';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('filament.panel.initial');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
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
                ])->columns(3),
                Section::make([
                    Forms\Components\Select::make('status')
                        ->label(__('filament.forms.fields.status'))
                        ->options([
                            'Approved' => 'Approve',
                            'On Hold' => 'Hold',
                        ])
                        ->columnSpanFull()
                        ->native(false),
                    Forms\Components\Textarea::make('initial_reason')
                        ->label(__('filament.forms.fields.initial_reason'))
                        ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.tables.columns.created_at'))
                    ->dateTime('d/m/Y (H:i:s)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request.wheels')
                    ->label(__('filament.tables.columns.wheels'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
                    ->visible(fn($record) => $record->status === 'Awaiting Approval'),
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
            'index' => Pages\ListInitials::route('/'),
            'create' => Pages\CreateInitial::route('/create'),
            'edit' => Pages\EditInitial::route('/{record}/edit'),
            'view' => Pages\ViewInitial::route('/{record}'),
        ];
    }
}
