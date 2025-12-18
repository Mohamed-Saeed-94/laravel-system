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
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Filament\Resources\DepartmentResource\Pages;
use Modules\Core\Models\Department;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationLabel = 'الإدارات';

    protected static string|\UnitEnum|null $navigationGroup = 'الهيكل التنظيمي';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralLabel = 'الإدارات';

    protected static ?string $label = 'إدارة';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('departments.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('departments.view') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->can('departments.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('departments.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('departments.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();

        return $user?->can('departments.delete_any') ?? false;
    }

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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
