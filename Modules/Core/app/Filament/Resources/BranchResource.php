<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Resources\BranchResource\Pages;
use Modules\Core\Models\Branch;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationLabel = null;
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 2;

    protected static ?string $pluralLabel = null;
    protected static ?string $label = null;

    public static function getNavigationLabel(): string
    {
        return __('core::branches.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('core::groups.basic_settings');
    }

    public static function getPluralLabel(): string
    {
        return __('core::branches.label');
    }

    public static function getLabel(): string
    {
        return __('core::branches.singular');
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
        return static::authUser()?->can('branches.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return static::authUser()?->can('branches.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::authUser()?->can('branches.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::authUser()?->can('branches.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::authUser()?->can('branches.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return static::authUser()?->can('branches.delete_any') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('city_id')
                ->label(__('core::branches.fields.city'))
                ->relationship('city', 'name_ar')
                ->required(),

            TextInput::make('name_ar')
                ->label(__('core::branches.fields.name_ar'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('name_en')
                ->label(__('core::branches.fields.name_en'))
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable(),

            Textarea::make('address')
                ->label(__('core::branches.fields.address'))
                ->rows(3)
                ->columnSpanFull()
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

                TextColumn::make('city.name_ar')
                    ->label(__('core::branches.fields.city'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_ar')
                    ->label(__('core::branches.fields.name_ar'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label(__('core::branches.fields.name_en'))
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
                CreateAction::make()
                    ->visible(fn (): bool => static::canCreate()),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => static::canDeleteAny()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
