<?php

namespace Tapp\FilamentForum\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ReportForumContentAction
{
    public static function make(string $name = 'report'): Action
    {
        return Action::make($name)
            ->label(__('filament-forum::filament-forum.reports.report'))
            ->icon('heroicon-o-flag')
            ->iconButton()
            ->color('gray')
            ->tooltip(__('filament-forum::filament-forum.reports.report'))
            ->modalHeading(__('filament-forum::filament-forum.reports.modal.heading'))
            ->modalDescription(__('filament-forum::filament-forum.reports.modal.description'))
            ->modalSubmitActionLabel(__('filament-forum::filament-forum.reports.modal.submit'))
            ->schema([
                Select::make('reason')
                    ->label(__('filament-forum::filament-forum.reports.reason'))
                    ->options(config('filament-forum.reports.reasons', []))
                    ->native(false)
                    ->required(),
                Textarea::make('details')
                    ->label(__('filament-forum::filament-forum.reports.details'))
                    ->rows(4)
                    ->maxLength(2000),
            ])
            ->visible(fn ($record = null): bool => Auth::check() && ($record === null || method_exists($record, 'reportContent')))
            ->disabled(fn ($record = null): bool => Auth::check() && $record !== null && $record->hasPendingReportBy(Auth::user()))
            ->action(function ($record, array $data): void {
                if (! Auth::check()) {
                    Notification::make()
                        ->title(__('filament-forum::filament-forum.reports.login-required'))
                        ->danger()
                        ->send();

                    return;
                }

                if ($record->hasPendingReportBy(Auth::user())) {
                    Notification::make()
                        ->title(__('filament-forum::filament-forum.reports.already-reported'))
                        ->warning()
                        ->send();

                    return;
                }

                $record->reportContent(
                    reporter: Auth::user(),
                    reason: $data['reason'] ?? null,
                    details: $data['details'] ?? null,
                );

                Notification::make()
                    ->title(__('filament-forum::filament-forum.reports.submitted'))
                    ->success()
                    ->send();
            });
    }
}
