<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Resources\CityResource\Pages;
use Modules\Core\Models\City;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationLabel = null;
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 1;

    protected static ?string $pluralLabel = null;
    protected static ?string $label = null;

    public static function getNavigationLabel(): string
    {
        return __('core::cities.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('core::groups.basic_settings');
    }

    public static function getPluralLabel(): string
    {
        return __('core::cities.label');
    }

    public static function getLabel(): string
    {
        return __('core::cities.singular');
    }

    /**
     * Use Filament auth to avoid IDE false-positives and to respect panel guard.
     */
    protected static function authUser(): ?Model
    {
        return filament()->auth()->user();
    }

    public static function canViewAny(): bool
    {
        return static::authUser()?->can('cities.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return static::authUser()?->can('cities.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::authUser()?->can('cities.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::authUser()?->can('cities.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::authUser()?->can('cities.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return static::authUser()?->can('cities.delete_any') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name_ar')
                ->label(__('core::cities.fields.name_ar'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('name_en')
                ->label(__('core::cities.fields.name_en'))
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable(),

            Toggle::make('is_active')
                ->label(__('core::fields.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('core::fields.id'))
                    ->sortable(),

                TextColumn::make('name_ar')
                    ->label(__('core::cities.fields.name_ar'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label(__('core::cities.fields.name_en'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('core::fields.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('core::fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('core::filters.status')),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Model $record): bool => static::canEdit($record)),
            ])
            ->toolbarActions([
                // زر الإضافة في أعلى الجدول
                CreateAction::make()
                    ->visible(fn (): bool => static::canCreate()),

                // حذف جماعي (يظهر فقط لو delete_any)
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => static::canDeleteAny()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit'   => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
