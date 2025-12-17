<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\CityResource\Pages;
use Modules\Core\Models\City;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationLabel = 'المدن';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات الأساسية';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralLabel = 'المدن';

    protected static ?string $label = 'مدينة';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name_ar')
                ->label('الاسم بالعربية')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('name_en')
                ->label('الاسم بالإنجليزية')
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable(),

            Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('المعرف')
                    ->sortable(),

                TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('الحالة'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
