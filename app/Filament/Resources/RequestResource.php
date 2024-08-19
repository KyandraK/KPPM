<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Request;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Mokhosh\FilamentRating\Components\Rating;
use App\Filament\Resources\RequestResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RequestResource\RelationManagers;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'mdi-file-document-edit-outline';

    public static function getModelLabel(): string
    {
        return __('filament.panel.request');
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->visible(fn($record) => $record && $record->rating !== null)
                    ->schema([
                        Rating::make('rating')
                            ->label('Rating')
                            ->size('lg')
                            ->disabled()
                            ->visible(fn($record) => $record && $record->rating !== null),
                        Forms\Components\Textarea::make('feedback')
                            ->label('Feedback')
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn($record) => $record && $record->feedback !== null),
                    ]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn() => Auth::user()->id),
                        Forms\Components\Select::make('wheels')
                            ->label(__('filament.forms.fields.wheels'))
                            ->options([
                                '6' => '6',
                                '4' => '4',
                                '3' => '3',
                                '2' => '2',
                                'Patroli' => 'Patroli',
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\DateTimePicker::make('departure_time')
                            ->label(__('filament.forms.fields.departure_time'))
                            ->required()
                            ->minDate(Carbon::today()),
                        Forms\Components\DateTimePicker::make('return_time')
                            ->label(__('filament.forms.fields.return_time'))
                            ->required()
                            ->minDate(Carbon::today()),
                        Forms\Components\Textarea::make('reason')
                            ->label(__('filament.forms.fields.reason'))
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make()
                    ->visible(fn($livewire) => $livewire instanceof Pages\ViewRequest)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Approved' => 'Approved',
                                'On Hold' => 'On Hold',
                                'Awaiting Approval' => 'Awaiting Approval',
                                'Waiting Approval by Kepala Div. Administrasi Umum' => 'Waiting Approval by Kepala Div. Administrasi Umum',
                                'Ready for Pickup' => 'Ready for Pickup',
                                'In Use' => 'In Use',
                                'Completed' => 'Completed',
                                'Canceled' => 'Canceled',
                            ])
                            ->columnSpanFull()
                            ->visible(fn($livewire) => $livewire instanceof Pages\ViewRequest),
                        Forms\Components\Textarea::make('approval_reason')
                            ->label(__('filament.forms.fields.approval_reason'))
                            ->columnSpanFull()
                            ->visible(fn($livewire) => $livewire instanceof Pages\ViewRequest),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.tables.columns.created_at'))
                    ->since()
                    ->searchable()
                    ->sortable()
                    ->dateTime('d/m/Y (H:i:s)'),
                Tables\Columns\TextColumn::make('wheels')
                    ->label(__('filament.tables.columns.wheels'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.tables.columns.status'))
                    ->badge()
                    ->colors([
                        'success' => 'Approved',
                        'danger' => 'On Hold',
                        'lime' => 'Waiting Approval by Kepala Div. Administrasi Umum',
                        'rose' => 'Canceled',
                        'info' => 'Ready for Pickup',
                        'gray' => 'Completed',
                    ]),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'Awaiting Approval'),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['status' => 'Canceled']);

                        $record->intials()->update(['status' => 'Canceled']);

                        $userDivision = Auth::user()->division;
                        $kepalaUsers = \App\Models\User::role('kepala')
                            ->where('division', $userDivision)
                            ->get();

                        $cancelingUser = Auth::user();

                        foreach ($kepalaUsers as $kepalaUser) {
                            Notification::make()
                                ->title(__('filament.notification.request_cancel_title'))
                                ->body(__('filament.notification.request_cancel_body', ['name' => $cancelingUser->name]))
                                ->sendToDatabase($kepalaUser);
                        }
                    })
                    ->visible(fn($record) => $record->status === 'Awaiting Approval')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('rating')
                    ->label('Rating')
                    ->icon('heroicon-o-star')
                    ->color('yellow')
                    ->visible(fn($record) => $record->status === 'Completed' && $record->rating === null)
                    ->form([
                        Rating::make('rating')
                            ->label(__('filament.forms.fields.rating_label'))
                            ->size('lg')
                            ->default(1)
                            ->required(),
                        Forms\Components\Textarea::make('feedback')
                            ->label(__('filament.forms.fields.feedback'))
                            ->columnSpan('full')
                            ->required(),
                    ])
                    ->action(function (array $data, Request $record): void {
                        $record->update([
                            'rating' => $data['rating'],
                            'feedback' => $data['feedback'],
                        ]);
                    }),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
            'view' => Pages\ViewRequest::route('/{record}'),
        ];
    }
}
