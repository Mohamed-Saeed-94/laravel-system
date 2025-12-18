<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Modules\Core\Filament\Resources\BranchDepartmentResource\Pages;
use Modules\Core\Models\BranchDepartment;

class BranchDepartmentResource extends Resource
{
    protected static ?string $model = BranchDepartment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('core::resources.branch_departments.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('core::groups.org_structure');
    }

    public static function getPluralLabel(): string
    {
        return __('core::resources.branch_departments.plural');
    }

    public static function getLabel(): string
    {
        return __('core::resources.branch_departments.label');
    }

    public static function canViewAny(): bool
    {
        return Gate::allows('branch_departments.view_any');
    }

    public static function canView(Model $record): bool
    {
        return Gate::allows('branch_departments.view');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('branch_departments.create');
    }

    public static function canEdit(Model $record): bool
    {
        return Gate::allows('branch_departments.update');
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::allows('branch_departments.delete');
    }

    public static function canDeleteAny(): bool
    {
        return Gate::allows('branch_departments.delete_any');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('branch_id')
                ->label(__('core::fields.branch'))
                ->relationship('branch', 'name_ar')
                ->searchable()
                ->required(),
            Select::make('department_id')
                ->label(__('core::fields.department'))
                ->relationship('department', 'name_ar')
                ->searchable()
                ->required()
                ->rule(fn ($get, ?Model $record) => Rule::unique('branch_departments')
                    ->where('branch_id', $get('branch_id'))
                    ->where('department_id', $get('department_id'))
                    ->ignore($record)),
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
                TextColumn::make('branch.name_ar')
                    ->label(__('core::fields.branch'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('department.name_ar')
                    ->label(__('core::fields.department'))
                    ->sortable()
                    ->searchable(),
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
                SelectFilter::make('branch_id')
                    ->label(__('core::fields.branch'))
                    ->relationship('branch', 'name_ar'),
                SelectFilter::make('department_id')
                    ->label(__('core::fields.department'))
                    ->relationship('department', 'name_ar'),
                TernaryFilter::make('is_active')
                    ->label(__('core::fields.is_active')),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Model $record): bool => static::canEdit($record)),
            ])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => static::canDeleteAny()),
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
