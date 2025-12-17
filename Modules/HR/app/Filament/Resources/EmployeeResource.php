<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Department;
use Modules\Core\Models\JobTitle;
use Modules\HR\Filament\Resources\EmployeeResource\Pages;
use Modules\HR\Models\Employee;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationLabel = 'الموظفون';

    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralLabel = 'الموظفون';

    protected static ?string $label = 'موظف';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('first_name')
                ->label('الاسم الأول')
                ->required()
                ->maxLength(255),
            TextInput::make('last_name')
                ->label('اسم العائلة')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('phone')
                ->label('رقم الجوال')
                ->tel()
                ->maxLength(50),
            DatePicker::make('hire_date')
                ->label('تاريخ التعيين')
                ->required(),
            Select::make('branch_id')
                ->label('الفرع')
                ->relationship('branch', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('department_id')
                ->label('الإدارة')
                ->relationship('department', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('job_title_id')
                ->label('المسمى الوظيفي')
                ->relationship('jobTitle', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
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
                TextColumn::make('first_name')
                    ->label('الاسم الأول')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('اسم العائلة')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                TextColumn::make('branch.name_ar')
                    ->label('الفرع')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('department.name_ar')
                    ->label('الإدارة')
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
                TextColumn::make('hire_date')
                    ->label('تاريخ التعيين')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name_ar'),
                SelectFilter::make('department_id')
                    ->label('الإدارة')
                    ->relationship('department', 'name_ar'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
