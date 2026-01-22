<?php

namespace App\Filament\Widgets;

use App\Models\Park;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestParks extends BaseWidget
{
    protected static ?string $heading = 'Parques Recentes';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Park::query()
                    ->with(['city.state'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Cidade')
                    ->formatStateUsing(fn($record) => $record->city?->name . ' - ' . $record->city?->state?->abbr),
                Tables\Columns\TextColumn::make('price_adult')
                    ->label('Preço')
                    ->money('BRL'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado')
                    ->since(),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->url(fn(Park $record): string => route('filament.admin.resources.parks.edit', $record))
                    ->icon('heroicon-o-pencil'),
            ]);
    }
}
