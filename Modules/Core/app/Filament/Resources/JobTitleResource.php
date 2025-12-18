<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
use Modules\Core\Filament\Resources\JobTitleResource\Pages;
use Modules\Core\Models\JobTitle;

class JobTitleResource extends Resource
{
    protected static ?string $model = JobTitle::class;

    protected static ?string $navigationLabel = 'المسميات الوظيفية';

    protected static string|\UnitEnum|null $navigationGroup = 'الهيكل التنظيمي';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralLabel = 'المسميات الوظيفية';

    protected static ?string $label = 'مسمى وظيفي';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('job_titles.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('job_titles.view') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->can('job_titles.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('job_titles.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('job_titles.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();

        return $user?->can('job_titles.delete_any') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('department_id')
                ->label('الإدارة')
                ->relationship('department', 'name_ar')
                ->required(),
            TextInput::make('name_ar')
                ->label('الاسم بالعربية')
                ->required()
                ->maxLength(255)
                ->rule(fn ( $get, ?Model $record) => Rule::unique('job_titles', 'name_ar')
                    ->where('department_id', $get('department_id'))
                    ->ignore($record)),
            TextInput::make('name_en')
                ->label('الاسم بالإنجليزية')
                ->maxLength(255)
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
                TextColumn::make('department.name_ar')
                    ->label('الإدارة')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListJobTitles::route('/'),
            'create' => Pages\CreateJobTitle::route('/create'),
            'edit' => Pages\EditJobTitle::route('/{record}/edit'),
        ];
    }
}
