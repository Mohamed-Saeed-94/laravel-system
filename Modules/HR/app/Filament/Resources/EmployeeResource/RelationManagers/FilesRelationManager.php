<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    // مهم لفيلامنت v4
    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // مهم علشان نعرف السجل الحالي أثناء Edit (بديل mountedTableActionRecord)
            Hidden::make('id'),

            Select::make('category')
                ->label('التصنيف')
                ->options([
                    'employee_photo' => 'صورة الموظف',
                    'identity_photo' => 'صورة الهوية',
                    'license_photo'  => 'صورة الرخصة',
                    'other'          => 'أخرى',
                ])
                ->required()
                ->native(false)
                ->live(),

            FileUpload::make('file_path')
                ->label('الملف')
                ->disk('public')
                ->directory('employees/files')
                ->preserveFilenames()
                ->required(fn (string $operation) => $operation === 'create')
                ->downloadable()
                ->openable()
                ->storeFileNamesIn('file_name')

                // خزّن الملف واملأ metadata (mime_type, file_size)
                ->saveUploadedFileUsing(function (UploadedFile $file, $livewire) {
                    $path = $file->storeAs(
                        'employees/files',
                        $file->getClientOriginalName(),
                        'public'
                    );

                    // تعبئة قيم الميتاداتا داخل الفورم
                    // (بنفس أسلوبك الحالي عشان ما يحصلش كسر)
                    $livewire->form->fill([
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);

                    return $path;
                }),

            Hidden::make('mime_type')->dehydrated(),
            Hidden::make('file_size')->dehydrated(),

            Select::make('side')
                ->label('الجهة')
                ->options([
                    'front' => 'أمام',
                    'back'  => 'خلف',
                    'other' => 'أخرى',
                ])
                ->native(false)
                ->nullable(),

            Toggle::make('is_primary')
                ->label('رئيسي')
                ->default(false)
                ->live()
                ->rules([
                    function ( $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (! $value) return;

                            $category = $get('category');
                            if ($category !== 'employee_photo') return;

                            $employeeId = $this->getOwnerRecord()->getKey();
                            $currentId  = $get('id'); // ✅ بديل mountedTableActionRecord

                            $exists = DB::table('employee_files')
                                ->where('fileable_type', $this->getOwnerRecord()::class)
                                ->where('fileable_id', $employeeId)
                                ->where('category', 'employee_photo')
                                ->where('is_primary', 1)
                                ->when($currentId, fn ($q) => $q->where('id', '!=', $currentId))
                                ->exists();

                            if ($exists) {
                                $fail('مسموح بصورة بروفايل واحدة فقط (Primary) للموظف.');
                            }
                        };
                    },
                ]),

            Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull()
                ->nullable(),
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
            ->recordActions([
                EditAction::make(),

                // ✅ احذف الملف من storage قبل حذف السجل
                \Filament\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if ($record?->file_path) {
                            try {
                                Storage::disk('public')->delete($record->file_path);
                            } catch (\Throwable) {
                                // تجاهل
                            }
                        }
                    }),
            ])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([
                    // ✅ Bulk delete: احذف كل الملفات من storage قبل حذف السجلات
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record?->file_path) {
                                    try {
                                        Storage::disk('public')->delete($record->file_path);
                                    } catch (\Throwable) {
                                        // تجاهل
                                    }
                                }
                            }
                        }),
                ]),
            ]);
    }
}
