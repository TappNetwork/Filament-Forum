<?php

namespace Tapp\FilamentForum\Filament\Resources\Forums\Pages;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Tapp\FilamentForum\Filament\Resources\Forums\ForumResource;

class ViewForum extends ManageRelatedRecords
{
    protected static string $resource = ForumResource::class;

    protected static string $relationship = 'posts';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
