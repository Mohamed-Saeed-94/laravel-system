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

    protected static ?string $navigationLabel = 'ربط الفروع بالإدارات';

    protected static string|\UnitEnum|null $navigationGroup = 'الهيكل التنظيمي';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $pluralLabel = 'ربط الفروع بالإدارات';

    protected static ?string $label = 'ربط فرع بإدارة';

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
                ->label('الفرع')
                ->relationship('branch', 'name_ar')
                ->required(),
            Select::make('department_id')
                ->label('الإدارة')
                ->relationship('department', 'name_ar')
                ->required()
                ->rule(fn ( $get, ?Model $record) => Rule::unique('branch_departments')
                    ->where('branch_id', $get('branch_id'))
                    ->where('department_id', $get('department_id'))
                    ->ignore($record)),
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
                TextColumn::make('branch.name_ar')
                    ->label('الفرع')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('department.name_ar')
                    ->label('الإدارة')
                    ->sortable()
                    ->searchable(),
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
                SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name_ar'),
                SelectFilter::make('department_id')
                    ->label('الإدارة')
                    ->relationship('department', 'name_ar'),
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
            'index' => Pages\ListBranchDepartments::route('/'),
            'create' => Pages\CreateBranchDepartment::route('/create'),
            'edit' => Pages\EditBranchDepartment::route('/{record}/edit'),
        ];
    }
}
