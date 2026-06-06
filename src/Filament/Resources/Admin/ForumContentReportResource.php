<?php

namespace Tapp\FilamentForum\Filament\Resources\Admin;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumContentReportResource\Pages\ListForumContentReports;
use Tapp\FilamentForum\Filament\Resources\Admin\ForumContentReportResource\Pages\ViewForumContentReport;
use Tapp\FilamentForum\Models\ForumContentReport;

class ForumContentReportResource extends Resource
{
    protected static ?string $model = ForumContentReport::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament-forum::filament-forum.reports.navigation-label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-forum::filament-forum.admin.navigation-group');
    }

    public static function isScopedToTenant(): bool
    {
        return config('filament-forum.tenancy.enabled', false);
    }

    public static function getTenantOwnershipRelationshipName(): string
    {
        if (! config('filament-forum.tenancy.enabled')) {
            return 'tenant';
        }

        return ForumContentReport::getTenantRelationshipName();
    }

    public static function getModelLabel(): string
    {
        return __('filament-forum::filament-forum.reports.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-forum::filament-forum.reports.plural-label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reportable_type')
                    ->label(__('filament-forum::filament-forum.reports.table.content-type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable(),
                TextColumn::make('reason')
                    ->label(__('filament-forum::filament-forum.reports.reason'))
                    ->formatStateUsing(fn (?string $state): string => config("filament-forum.reports.reasons.{$state}", $state ?? ''))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament-forum::filament-forum.reports.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ForumContentReport::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ForumContentReport::STATUS_RESOLVED => 'success',
                        ForumContentReport::STATUS_DISMISSED => 'gray',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('reporter.name')
                    ->label(__('filament-forum::filament-forum.reports.table.reporter'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament-forum::filament-forum.reports.table.reported-at'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament-forum::filament-forum.reports.status'))
                    ->options(ForumContentReport::statuses()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    static::resolveAction(),
                    static::dismissAction(),
                ])->tooltip(__('filament-forum::filament-forum.reports.actions')),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('filament-forum::filament-forum.reports.content'))
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('reportable_type')
                                    ->label(__('filament-forum::filament-forum.reports.table.content-type'))
                                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                                TextEntry::make('details')
                                    ->label(__('filament-forum::filament-forum.reports.details'))
                                    ->placeholder(__('filament-forum::filament-forum.reports.no-details'))
                                    ->columnSpanFull(),
                                TextEntry::make('reportable.name')
                                    ->label(__('filament-forum::filament-forum.forum-post.table.label.name'))
                                    ->placeholder(__('filament-forum::filament-forum.forum-post.unknown')),
                                TextEntry::make('reportable.content')
                                    ->label(__('filament-forum::filament-forum.comments.edit-comment'))
                                    ->html()
                                    ->prose()
                                    ->placeholder(__('filament-forum::filament-forum.forum-post.unknown'))
                                    ->columnSpanFull(),
                            ]),
                        Section::make(__('filament-forum::filament-forum.reports.review'))
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('filament-forum::filament-forum.reports.status'))
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => ForumContentReport::statuses()[$state] ?? $state),
                                TextEntry::make('reason')
                                    ->label(__('filament-forum::filament-forum.reports.reason'))
                                    ->formatStateUsing(fn (?string $state): string => config("filament-forum.reports.reasons.{$state}", $state ?? '')),
                                TextEntry::make('reporter.name')
                                    ->label(__('filament-forum::filament-forum.reports.table.reporter')),
                                TextEntry::make('reviewer.name')
                                    ->label(__('filament-forum::filament-forum.reports.reviewer'))
                                    ->placeholder(__('filament-forum::filament-forum.reports.not-reviewed')),
                                TextEntry::make('resolution_note')
                                    ->label(__('filament-forum::filament-forum.reports.resolution-note'))
                                    ->placeholder(__('filament-forum::filament-forum.reports.no-resolution-note')),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumContentReports::route('/'),
            'view' => ViewForumContentReport::route('/{record}'),
        ];
    }

    public static function resolveAction(): Action
    {
        return Action::make('resolve')
            ->label(__('filament-forum::filament-forum.reports.resolve'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (ForumContentReport $record): bool => $record->isPending())
            ->schema([
                Textarea::make('resolution_note')
                    ->label(__('filament-forum::filament-forum.reports.resolution-note'))
                    ->rows(3)
                    ->maxLength(2000),
            ])
            ->action(function (ForumContentReport $record, array $data): void {
                $record->resolve(Auth::user(), $data['resolution_note'] ?? null);
            });
    }

    public static function dismissAction(): Action
    {
        return Action::make('dismiss')
            ->label(__('filament-forum::filament-forum.reports.dismiss'))
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (ForumContentReport $record): bool => $record->isPending())
            ->schema([
                Textarea::make('resolution_note')
                    ->label(__('filament-forum::filament-forum.reports.resolution-note'))
                    ->rows(3)
                    ->maxLength(2000),
            ])
            ->action(function (ForumContentReport $record, array $data): void {
                $record->dismiss(Auth::user(), $data['resolution_note'] ?? null);
            });
    }
}
