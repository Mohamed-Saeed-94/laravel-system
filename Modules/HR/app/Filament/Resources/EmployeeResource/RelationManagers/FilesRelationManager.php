<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category')
                ->label('التصنيف')
                ->options([
                    'employee_photo' => 'صورة الموظف',
                    'identity_photo' => 'صورة الهوية',
                    'license_photo' => 'صورة الرخصة',
                    'other' => 'أخرى',
                ])
                ->required()
                ->native(false),
            FileUpload::make('file_path')
                ->label('الملف')
                ->disk('public')
                ->directory('employees/files')
                ->preserveFilenames()
                ->storeFileNamesIn('file_name')
                ->required(fn (string $operation) => $operation === 'create')
                ->downloadable()
                ->openable(),
            Select::make('side')
                ->label('الجهة')
                ->options([
                    'front' => 'أمام',
                    'back' => 'خلف',
                    'other' => 'أخرى',
                ])
                ->native(false),
            Toggle::make('is_primary')
                ->label('رئيسي')
                ->default(false),
            Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge(),
                TextColumn::make('file_name')
                    ->label('اسم الملف')
                    ->limit(40),
                TextColumn::make('side')
                    ->label('الجهة')
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
