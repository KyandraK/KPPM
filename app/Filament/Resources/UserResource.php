<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getModelLabel(): string
    {
        return __('filament.panel.user');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.panel.user_management');
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament.forms.fields.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('filament.forms.fields.email'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('division')
                    ->options([
                        'divisi 1' => 'Divisi 1',
                        'divisi 2' => 'Divisi 2',
                        'divisi 3' => 'Divisi 3',
                        'divisi 4' => 'Divisi 4',
                        'divisi 5' => 'Divisi 5',
                        'divisi 6' => 'Divisi 6',
                    ])
                    ->native(false),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->searchable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('password')
                    ->label(__('filament.forms.fields.password'))
                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser)
                    ->password()
                    ->revealable()
                    ->columnSpanFull()
                    ->required(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.tables.columns.user_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.tables.columns.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.tables.columns.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('filament.tables.columns.role'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return ucwords(str_replace('_', ' ', $state));
                    })
                    ->colors([
                        'success' => 'super_admin',
                        'gray' => 'panel_user',
                        'primary' => 'petugas',
                        'info' => 'kepala',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('reset')
                        ->label('Reset')
                        ->icon('heroicon-o-key')
                        ->color('gray')
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->label(__('filament.forms.fields.reset'))
                                ->password()
                                ->revealable()
                                ->required(),
                        ])
                        ->action(function (array $data, User $record): void {
                            $record->password = bcrypt($data['password']);
                            $record->save();
                        }),
                ])
                    ->color('gray')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
