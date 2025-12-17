<?php

namespace Modules\Core\App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Modules\Core\App\Filament\Resources\BranchDepartmentResource\Pages;
use Modules\Core\App\Models\BranchDepartment;

class BranchDepartmentResource extends Resource
{
    protected static ?string $model = BranchDepartment::class;

    protected static string|\UnitEnum|null $navigationGroup = 'الربط الوظيفي';

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $pluralLabel = 'ربط الفروع بالإدارات';

    protected static ?string $label = 'ربط فرع بإدارة';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('branch_id')
                ->label('الفرع')
                ->relationship('branch', 'name_ar')
                ->required(),
            Forms\Components\Select::make('department_id')
                ->label('الإدارة')
                ->relationship('department', 'name_ar')
                ->required(),
            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->rules([
            'branch_id' => [
                'required',
            ],
            'department_id' => [
                'required',
                fn (Get $get, ?Model $record) => Rule::unique('branch_departments')
                    ->where('branch_id', $get('branch_id'))
                    ->where('department_id', $get('department_id'))
                    ->ignore($record),
            ],
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('المعرف')
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name_ar')
                    ->label('الفرع')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name_ar')
                    ->label('الإدارة')
                    ->sortable()
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name_ar'),
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('الإدارة')
                    ->relationship('department', 'name_ar'),
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
            'index' => Pages\ListBranchDepartments::route('/'),
            'create' => Pages\CreateBranchDepartment::route('/create'),
            'edit' => Pages\EditBranchDepartment::route('/{record}/edit'),
        ];
    }
}
