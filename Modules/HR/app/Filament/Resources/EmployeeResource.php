<?php

namespace Modules\HR\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('first_name')
                ->label('الاسم الأول')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('last_name')
                ->label('اسم العائلة')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->label('رقم الجوال')
                ->tel()
                ->maxLength(50),
            Forms\Components\DatePicker::make('hire_date')
                ->label('تاريخ التعيين')
                ->required(),
            Forms\Components\Select::make('branch_id')
                ->label('الفرع')
                ->relationship('branch', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('department_id')
                ->label('الإدارة')
                ->relationship('department', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('job_title_id')
                ->label('المسمى الوظيفي')
                ->relationship('jobTitle', 'name_ar')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('المعرف')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('الاسم الأول')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('اسم العائلة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name_ar')
                    ->label('الفرع')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name_ar')
                    ->label('الإدارة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jobTitle.name_ar')
                    ->label('المسمى الوظيفي')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('تاريخ التعيين')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name_ar'),
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('الإدارة')
                    ->relationship('department', 'name_ar'),
                Tables\Filters\SelectFilter::make('job_title_id')
                    ->label('المسمى الوظيفي')
                    ->relationship('jobTitle', 'name_ar'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
