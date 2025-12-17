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
use Modules\Core\App\Filament\Resources\BranchJobTitleResource\Pages;
use Modules\Core\App\Models\BranchJobTitle;

class BranchJobTitleResource extends Resource
{
    protected static ?string $model = BranchJobTitle::class;

    protected static string|\UnitEnum|null $navigationGroup = 'الربط الوظيفي';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $pluralLabel = 'ربط الفروع بالمسميات الوظيفية';

    protected static ?string $label = 'ربط فرع بمسمى وظيفي';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('branch_id')
                ->label('الفرع')
                ->relationship('branch', 'name_ar')
                ->required(),
            Forms\Components\Select::make('job_title_id')
                ->label('المسمى الوظيفي')
                ->relationship('jobTitle', 'name_ar')
                ->required(),
            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->rules([
            'branch_id' => ['required'],
            'job_title_id' => [
                'required',
                fn (Get $get, ?Model $record) => Rule::unique('branch_job_titles')
                    ->where('branch_id', $get('branch_id'))
                    ->where('job_title_id', $get('job_title_id'))
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
                Tables\Columns\TextColumn::make('jobTitle.name_ar')
                    ->label('المسمى الوظيفي')
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
                Tables\Filters\SelectFilter::make('job_title_id')
                    ->label('المسمى الوظيفي')
                    ->relationship('jobTitle', 'name_ar'),
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
            'index' => Pages\ListBranchJobTitles::route('/'),
            'create' => Pages\CreateBranchJobTitle::route('/create'),
            'edit' => Pages\EditBranchJobTitle::route('/{record}/edit'),
        ];
    }
}
