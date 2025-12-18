<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\HR\Models\EmployeePhone;

class PhonesRelationManager extends RelationManager
{
    protected static string $relationship = 'phones';

    // مهم لفيلامنت v4
    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // ✅ علشان نعرف السجل الحالي أثناء Edit بدل mountedTableActionRecord
            Hidden::make('id'),

            TextInput::make('phone')
                ->label('رقم الجوال')
                ->required()
                ->maxLength(30)
                ->unique(
                    table: EmployeePhone::class,
                    column: 'phone',
                    ignoreRecord: true,
                    modifyRuleUsing: function (Rule $rule) {
                        $employeeId = $this->getOwnerRecord()->getKey();
                        return $rule->where('employee_id', $employeeId);
                    }
                ),

            Select::make('type')
                ->label('النوع')
                ->options([
                    'personal'  => 'شخصي',
                    'work'      => 'عمل',
                    'emergency' => 'طوارئ',
                ])
                ->default('personal')
                ->native(false)
                ->required(),

            Toggle::make('is_primary')
                ->label('رئيسي')
                ->default(false)
                ->live()
                // ✅ منع أكثر من Primary واحد لنفس الموظف
                ->rules([
                    function ( $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (! $value) return;

                            $employeeId = $this->getOwnerRecord()->getKey();
                            $currentId  = $get('id'); // ✅ بديل mountedTableActionRecord

                            $exists = DB::table('employee_phones')
                                ->where('employee_id', $employeeId)
                                ->where('is_primary', 1)
                                ->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))
                                ->exists();

                            if ($exists) {
                                $fail('مسموح برقم رئيسي واحد فقط للموظف.');
                            }
                        };
                    },
                ]),
        ])->columns(2);
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
