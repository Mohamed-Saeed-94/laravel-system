<?php

namespace Modules\Core\Filament\Resources;

use Filament\Actions\BulkActionGroup;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Modules\Core\Filament\Resources\BranchResource\Pages;
use Modules\Core\Models\Branch;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationLabel = 'الفروع';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات الأساسية';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralLabel = 'الفروع';

    protected static ?string $label = 'فرع';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('branches.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('branches.view') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->can('branches.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('branches.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user?->can('branches.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->user();

        return $user?->can('branches.delete_any') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('city_id')
                ->label('المدينة')
                ->relationship('city', 'name_ar')
                ->required(),
            TextInput::make('name_ar')
                ->label('الاسم بالعربية')
                ->required()
                ->maxLength(255)
                ->rule(fn ($get, ?Model $record) => Rule::unique('branches', 'name_ar')
                    ->where('city_id', $get('city_id'))
                    ->ignore($record)),
            TextInput::make('name_en')
                ->label('الاسم بالإنجليزية')
                ->maxLength(255)
                ->nullable(),
            Textarea::make('address')
                ->label('العنوان')
                ->rows(3)
                ->columnSpanFull()
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
                TextColumn::make('city.name_ar')
                    ->label('المدينة')
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
                SelectFilter::make('city_id')
                    ->label('المدينة')
                    ->relationship('city', 'name_ar'),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
