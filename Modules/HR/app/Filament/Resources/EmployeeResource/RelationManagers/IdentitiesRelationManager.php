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
use Filament\Forms\Get;
use Modules\HR\Models\EmployeeIdentity;

class IdentitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'identities';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('type')
                ->label('النوع')
                ->options([
                    'iqama' => 'إقامة',
                    'saudi_national_id' => 'هوية وطنية',
                    'passport' => 'جواز سفر',
                ])
                ->required()
                ->native(false),
            TextInput::make('number')
                ->label('الرقم')
                ->required()
                ->maxLength(30)
                ->rules(function (Get $get) {
                    return [
                        function (string $attribute, $value, $fail) use ($get) {
                            $type = $get('type');

                            if (! $type || ! $value) {
                                return;
                            }

                            $exists = EmployeeIdentity::query()
                                ->where('type', $type)
                                ->where('number', $value)
                                ->when($this->record?->id, fn ($query) => $query->where('id', '<>', $this->record->id))
                                ->exists();

                            if ($exists) {
                                $fail(__('The identity number has already been taken for this type.'));
                            }
                        },
                    ];
                }),
            TextInput::make('sponsor_name')
                ->label('اسم الكفيل')
                ->maxLength(200),
            TextInput::make('sponsor_id_number')
                ->label('رقم هوية الكفيل')
                ->maxLength(30),
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
