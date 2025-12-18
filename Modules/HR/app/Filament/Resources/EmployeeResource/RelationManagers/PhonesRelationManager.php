<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Modules\HR\Models\EmployeePhone;

class PhonesRelationManager extends RelationManager
{
    protected static string $relationship = 'phones';

    public function form(Form $form): Form
    {
        $employeeId = $this->getOwnerRecord()->getKey();

        return $form->schema([
            TextInput::make('phone')
                ->label('رقم الجوال')
                ->required()
                ->maxLength(30)
                ->unique(table: EmployeePhone::class, column: 'phone', ignoreRecord: true, modifyRuleUsing: fn (Rule $rule) => $rule->where('employee_id', $employeeId)),
            Select::make('type')
                ->label('النوع')
                ->options([
                    'personal' => 'شخصي',
                    'work' => 'عمل',
                    'emergency' => 'طوارئ',
                ])
                ->default('personal')
                ->native(false),
            Toggle::make('is_primary')
                ->label('رئيسي')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')
                    ->label('رقم الجوال')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
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
