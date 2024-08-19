<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ApprovalResource;
use Spatie\Permission\Models\Role;

class EditApproval extends EditRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $approval = $this->record;

        if ($approval->status === 'Approved') {
            Notification::make()
                ->title(__('filament.notification.approved_approval'))
                ->body(__('filament.notification.approved_approval_body'))
                ->sendToDatabase($approval->request->user);

            $petugasUsers = Role::where('name', 'petugas')
                ->first()
                ->users()
                ->get();

            foreach ($petugasUsers as $petugasUser) {
                Notification::make()
                    ->title(__('filament.notification.pool_approval'))
                    ->body(__('filament.notification.pool_approval_body'))
                    ->sendToDatabase($petugasUser);
            }
        }

        if ($approval->status === 'On Hold') {
            Notification::make()
                ->title(__('filament.notification.hold_approval'))
                ->body(__('filament.notification.hold_approval_body'))
                ->sendToDatabase($approval->request->user);
        }
    }
}
