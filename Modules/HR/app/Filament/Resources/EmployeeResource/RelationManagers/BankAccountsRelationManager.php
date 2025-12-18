<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Modules\HR\Models\EmployeeBankAccount;

class BankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'bankAccounts';

    // مهم لفيلامنت v4
    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('bank_name')
                ->label('اسم البنك')
                ->maxLength(255)
                ->nullable(),

            TextInput::make('account_holder_name')
                ->label('اسم صاحب الحساب')
                ->maxLength(255)
                ->nullable(),

            TextInput::make('account_number')
                ->label('رقم الحساب')
                ->required()
                ->maxLength(50)
                ->unique(
                    table: EmployeeBankAccount::class,
                    column: 'account_number',
                    ignoreRecord: true,
                    modifyRuleUsing: function (Rule $rule) {
                        $employeeId = $this->getOwnerRecord()->getKey();

                        return $rule->where('employee_id', $employeeId);
                    }
                ),

            TextInput::make('iban')
                ->label('IBAN')
                ->maxLength(34)
                ->nullable(),

            TextInput::make('swift_code')
                ->label('SWIFT')
                ->maxLength(255)
                ->nullable(),

            Toggle::make('is_primary')
                ->label('حساب رئيسي')
                ->default(false),

            Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->columns(2);
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
