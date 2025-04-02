<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrackResource\Pages;
use App\Models\Track;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrackResource extends Resource
{
    protected static ?string $model = Track::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover')
                    ->required()
                    ->disk('telegram')
                    ->directory('covers')
                    ->label('Cover')
                    ->columnSpanFull(),
                Forms\Components\Select::make('artists')
                    ->relationship('artists', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('isrc')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('duration')
                    ->columnSpanFull()
                    ->required()
                    ->numeric()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('files')
                    ->multiple()
                    ->disk('telegram')
                    ->directory('tracks')
                    ->label('Files')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('cover') 
                    ->disk('telegram')
                    ->height(50)
                    ->square(),
                Tables\Columns\TextColumn::make('isrc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTracks::route('/'),
            'create' => Pages\CreateTrack::route('/create'),
            'edit' => Pages\EditTrack::route('/{record}/edit'),
        ];
    }
}
