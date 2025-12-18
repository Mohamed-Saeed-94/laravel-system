<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Modules\HR\Models\EmployeeBankAccount;

class BankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'bankAccounts';

    public function form(Form $form): Form
    {
        $employeeId = $this->getOwnerRecord()->getKey();

        return $form->schema([
            TextInput::make('bank_name')
                ->label('اسم البنك')
                ->maxLength(255),
            TextInput::make('account_holder_name')
                ->label('اسم صاحب الحساب')
                ->maxLength(255),
            TextInput::make('account_number')
                ->label('رقم الحساب')
                ->required()
                ->maxLength(50)
                ->unique(table: EmployeeBankAccount::class, column: 'account_number', ignoreRecord: true, modifyRuleUsing: fn (Rule $rule) => $rule->where('employee_id', $employeeId)),
            TextInput::make('iban')
                ->label('IBAN')
                ->maxLength(34),
            TextInput::make('swift_code')
                ->label('SWIFT')
                ->maxLength(255),
            Toggle::make('is_primary')
                ->label('حساب رئيسي')
                ->default(false),
            Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')
                    ->label('اسم البنك')
                    ->searchable(),
                TextColumn::make('account_holder_name')
                    ->label('صاحب الحساب')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->label('رقم الحساب')
                    ->searchable(),
                TextColumn::make('iban')
                    ->label('IBAN')
                    ->toggleable(),
                IconColumn::make('is_primary')
                    ->label('رئيسي')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
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
