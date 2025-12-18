<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\HR\Models\EmployeeIdentity;

class IdentitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'identities';

    // مهم لفيلامنت v4
    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // لضمان توفر id أثناء التعديل (Edit)
            Hidden::make('id'),

            Select::make('type')
                ->label('النوع')
                ->options([
                    'iqama'             => 'إقامة',
                    'saudi_national_id' => 'هوية وطنية',
                    'passport'          => 'جواز سفر',
                ])
                ->required()
                ->native(false)
                ->live(),

            TextInput::make('number')
                ->label('الرقم')
                ->required()
                ->maxLength(30)
                ->live()
                ->rules([
                    function ( $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $type = $get('type');

                            if (! $type || ! $value) {
                                return;
                            }

                            // أثناء Edit هنلاقي id هنا (بسبب Hidden::make('id'))
                            $currentId = $get('id');

                            $exists = EmployeeIdentity::query()
                                ->where('type', $type)
                                ->where('number', $value)
                                ->when($currentId, fn ($q) => $q->whereKeyNot($currentId))
                                ->exists();

                            if ($exists) {
                                $fail('رقم الهوية مستخدم بالفعل لهذا النوع.');
                            }
                        };
                    },
                ]),

            TextInput::make('sponsor_name')
                ->label('اسم الكفيل')
                ->maxLength(200)
                ->nullable(),

            TextInput::make('sponsor_id_number')
                ->label('رقم هوية الكفيل')
                ->maxLength(30)
                ->nullable(),

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

                TextColumn::make('sponsor_name')
                    ->label('اسم الكفيل')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sponsor_id_number')
                    ->label('رقم هوية الكفيل')
                    ->toggleable(isToggledHiddenByDefault: true),

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
