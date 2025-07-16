<?php

namespace Tapp\FilamentForum\Filament\Resources\ForumResource\Pages;

use Tapp\FilamentForum\Filament\Resources\ForumResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use BackedEnum;

class ViewForum extends ManageRelatedRecords
{
    protected static string $resource = ForumResource::class;
    protected static string $relationship = 'posts';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
} 