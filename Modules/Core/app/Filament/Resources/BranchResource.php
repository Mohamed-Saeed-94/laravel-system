<?php

namespace Modules\Core\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Modules\Core\Filament\Resources\BranchResource\Pages;
use Modules\Core\Models\Branch;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationLabel = 'الفروع';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات الأساسية';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralLabel = 'الفروع';

    protected static ?string $label = 'فرع';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('city_id')
                ->label('المدينة')
                ->relationship('city', 'name_ar')
                ->required(),
            Forms\Components\TextInput::make('name_ar')
                ->label('الاسم بالعربية')
                ->required()
                ->maxLength(255)
                ->rule(fn (Get $get, ?Model $record) => Rule::unique('branches', 'name_ar')
                    ->where('city_id', $get('city_id'))
                    ->ignore($record)),
            Forms\Components\TextInput::make('name_en')
                ->label('الاسم بالإنجليزية')
                ->maxLength(255)
                ->nullable(),
            Forms\Components\Textarea::make('address')
                ->label('العنوان')
                ->rows(3)
                ->columnSpanFull()
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
                Tables\Columns\TextColumn::make('city.name_ar')
                    ->label('المدينة')
                    ->sortable()
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('المدينة')
                    ->relationship('city', 'name_ar'),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
