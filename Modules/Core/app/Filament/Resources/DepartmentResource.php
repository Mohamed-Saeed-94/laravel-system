<?php

namespace Modules\Core\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name_ar')
                ->label('الاسم بالعربية')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name_en')
                ->label('الاسم بالإنجليزية')
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->nullable(),
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
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم بالعربية')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
