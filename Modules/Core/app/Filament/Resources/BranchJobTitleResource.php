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
use Modules\Core\Filament\Resources\BranchJobTitleResource\Pages;
use Modules\Core\Models\BranchJobTitle;

class BranchJobTitleResource extends Resource
{
    protected static ?string $model = BranchJobTitle::class;

    protected static ?string $navigationLabel = 'ربط الفروع بالمسميات';

    protected static string|\UnitEnum|null $navigationGroup = 'الهيكل التنظيمي';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $pluralLabel = 'ربط الفروع بالمسميات الوظيفية';

    protected static ?string $label = 'ربط فرع بمسمى وظيفي';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('branch_id')
                ->label('الفرع')
                ->relationship('branch', 'name_ar')
                ->required(),
            Select::make('job_title_id')
                ->label('المسمى الوظيفي')
                ->relationship('jobTitle', 'name_ar')
                ->required()
                ->rule(fn ( $get, ?Model $record) => Rule::unique('branch_job_titles')
                    ->where('branch_id', $get('branch_id'))
                    ->where('job_title_id', $get('job_title_id'))
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
                TextColumn::make('jobTitle.name_ar')
                    ->label('المسمى الوظيفي')
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
                SelectFilter::make('job_title_id')
                    ->label('المسمى الوظيفي')
                    ->relationship('jobTitle', 'name_ar'),
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
            'index' => Pages\ListBranchJobTitles::route('/'),
            'create' => Pages\CreateBranchJobTitle::route('/create'),
            'edit' => Pages\EditBranchJobTitle::route('/{record}/edit'),
        ];
    }
}
