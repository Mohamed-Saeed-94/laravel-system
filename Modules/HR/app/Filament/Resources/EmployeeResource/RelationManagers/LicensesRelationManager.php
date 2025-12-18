<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Modules\HR\Models\EmployeeLicense;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    // مهم لفيلامنت v4
    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('النوع')
                ->options([
                    'private'          => 'خصوصي',
                    'motorcycle'       => 'دراجة نارية',
                    'public_transport' => 'نقل عام',
                    'other'            => 'أخرى',
                ])
                ->required()
                ->native(false),

            TextInput::make('number')
                ->label('الرقم')
                ->required()
                ->maxLength(30)
                ->unique(
                    table: EmployeeLicense::class,
                    column: 'number',
                    ignoreRecord: true,
                    modifyRuleUsing: function (Rule $rule) {
                        $employeeId = $this->getOwnerRecord()->getKey();
                        return $rule->where('employee_id', $employeeId);
                    }
                ),

            DatePicker::make('issue_date')
                ->label('تاريخ الإصدار')
                ->nullable(),

            DatePicker::make('expiry_date')
                ->label('تاريخ الانتهاء')
                ->nullable(),

            Toggle::make('is_primary')
                ->label('رئيسي')
                ->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),

                TextColumn::make('number')
                    ->label('الرقم')
                    ->searchable(),

                TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date(),

                IconColumn::make('is_primary')
                    ->label('رئيسي')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
