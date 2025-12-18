<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Modules\Core\Filament\Resources\BranchDepartmentResource\Pages;
use Modules\Core\Models\BranchDepartment;

class BranchDepartmentResource extends Resource
{
    protected static ?string $model = BranchDepartment::class;

    protected static ?string $navigationLabel = null;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $pluralLabel = null;

    protected static ?string $label = null;

    public static function getNavigationLabel(): string
    {
        return __('core::branch_departments.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('core::core.groups.org_structure');
    }

    public static function getPluralLabel(): string
    {
        return __('core::branch_departments.label');
    }

    public static function getLabel(): string
    {
        return __('core::branch_departments.singular');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('branch_departments.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('branch_departments.view') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->can('branch_departments.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('branch_departments.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('branch_departments.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();

        return $user?->can('branch_departments.delete_any') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('branch_id')
                ->label(__('core::branch_departments.fields.branch'))
                ->relationship('branch', 'name_ar')
                ->required(),
            Select::make('department_id')
                ->label(__('core::branch_departments.fields.department'))
                ->relationship('department', 'name_ar')
                ->required()
                ->rule(fn (Get $get, ?Model $record) => Rule::unique('branch_departments')
                    ->where('branch_id', $get('branch_id'))
                    ->where('department_id', $get('department_id'))
                    ->ignore($record)),
            Toggle::make('is_active')
                ->label(__('core::core.fields.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('core::core.fields.id'))
                    ->sortable(),
                TextColumn::make('branch.name_ar')
                    ->label(__('core::branch_departments.fields.branch'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('department.name_ar')
                    ->label(__('core::branch_departments.fields.department'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('core::core.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('core::core.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label(__('core::branch_departments.filters.branch'))
                    ->relationship('branch', 'name_ar'),
                SelectFilter::make('department_id')
                    ->label(__('core::branch_departments.filters.department'))
                    ->relationship('department', 'name_ar'),
                TernaryFilter::make('is_active')
                    ->label(__('core::core.filters.status')),
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
            'index' => Pages\ListBranchDepartments::route('/'),
            'create' => Pages\CreateBranchDepartment::route('/create'),
            'edit' => Pages\EditBranchDepartment::route('/{record}/edit'),
        ];
    }
}
