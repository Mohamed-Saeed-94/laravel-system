<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\HR\Models\EmployeeLicense;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    public function form(Form $form): Form
    {
        $employeeId = $this->getOwnerRecord()->getKey();

        return $form->schema([
            Select::make('type')
                ->label('النوع')
                ->options([
                    'private' => 'خصوصي',
                    'motorcycle' => 'دراجة نارية',
                    'public_transport' => 'نقل عام',
                    'other' => 'أخرى',
                ])
                ->required()
                ->native(false),
            TextInput::make('number')
                ->label('الرقم')
                ->required()
                ->maxLength(30)
                ->unique(table: EmployeeLicense::class, column: 'number', ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('employee_id', $employeeId)),
            DatePicker::make('issue_date')
                ->label('تاريخ الإصدار'),
            DatePicker::make('expiry_date')
                ->label('تاريخ الانتهاء'),
            Toggle::make('is_primary')
                ->label('رئيسي')
                ->default(true),
        ]);
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
