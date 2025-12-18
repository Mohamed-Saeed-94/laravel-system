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
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Modules\Core\Filament\Resources\BranchJobTitleResource\Pages;
use Modules\Core\Models\BranchJobTitle;

class BranchJobTitleResource extends Resource
{
    protected static ?string $model = BranchJobTitle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('core::resources.branch_job_titles.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('core::groups.org_structure');
    }

    public static function getPluralLabel(): string
    {
        return __('core::resources.branch_job_titles.plural');
    }

    public static function getLabel(): string
    {
        return __('core::resources.branch_job_titles.label');
    }

    public static function canViewAny(): bool
    {
        return Gate::allows('branch_job_titles.view_any');
    }

    public static function canView(Model $record): bool
    {
        return Gate::allows('branch_job_titles.view');
    }

    public static function canCreate(): bool
    {
        return Gate::allows('branch_job_titles.create');
    }

    public static function canEdit(Model $record): bool
    {
        return Gate::allows('branch_job_titles.update');
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::allows('branch_job_titles.delete');
    }

    public static function canDeleteAny(): bool
    {
        return Gate::allows('branch_job_titles.delete_any');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('branch_id')
                ->label(__('core::fields.branch'))
                ->relationship('branch', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('job_title_id')
                ->label(__('core::fields.job_title'))
                ->relationship('jobTitle', 'name_ar')
                ->searchable()
                ->preload()
                ->required()
                ->rule(fn ($get, ?Model $record) => Rule::unique('branch_job_titles')
                    ->where('branch_id', $get('branch_id'))
                    ->where('job_title_id', $get('job_title_id'))
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
                TextColumn::make('jobTitle.name_ar')
                    ->label(__('core::fields.job_title'))
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
                SelectFilter::make('job_title_id')
                    ->label(__('core::fields.job_title'))
                    ->relationship('jobTitle', 'name_ar'),
                TernaryFilter::make('is_active')
                    ->label(__('core::fields.is_active')),
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
            'index' => Pages\ListBranchJobTitles::route('/'),
            'create' => Pages\CreateBranchJobTitle::route('/create'),
            'edit' => Pages\EditBranchJobTitle::route('/{record}/edit'),
        ];
    }
}
