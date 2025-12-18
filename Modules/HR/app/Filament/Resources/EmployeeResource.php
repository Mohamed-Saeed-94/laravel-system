<?php

namespace Modules\HR\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Department;
use Modules\Core\Models\JobTitle;
use Modules\HR\Filament\Resources\EmployeeResource\Pages;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\BankAccountsRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\FilesRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\IdentitiesRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\LicensesRelationManager;
use Modules\HR\Filament\Resources\EmployeeResource\RelationManagers\PhonesRelationManager;
use Modules\HR\Models\Employee;
use Modules\HR\Rules\DepartmentInBranch;
use Modules\HR\Rules\JobTitleInBranch;
use Modules\HR\Rules\JobTitleMatchesDepartment;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationLabel = 'الموظفون';

    protected static string|\UnitEnum|null $navigationGroup = 'الموظفين';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralLabel = 'الموظفون';

    protected static ?string $label = 'موظف';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('employee_code')
                ->label('كود الموظف')
                ->required()
                ->maxLength(30)
                ->unique(ignoreRecord: true),
            TextInput::make('full_name')
                ->label('الاسم الكامل')
                ->required()
                ->maxLength(200),
            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->maxLength(255),
            Select::make('gender')
                ->label('الجنس')
                ->options([
                    'male' => 'ذكر',
                    'female' => 'أنثى',
                ])
                ->native(false),
            Select::make('branch_id')
                ->label('الفرع')
                ->options(fn () => Branch::query()->where('is_active', true)->orderBy('name_ar')->pluck('name_ar', 'id'))
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Set $set) {
                    $set('department_id', null);
                    $set('job_title_id', null);
                }),
            Select::make('department_id')
                ->label('الإدارة')
                ->options(function (Get $get) {
                    $branchId = $get('branch_id');

                    if (! $branchId) {
                        return [];
                    }

                    return Department::query()
                        ->where('departments.is_active', true)
                        ->whereIn('departments.id', function ($query) use ($branchId) {
                            $query->select('department_id')
                                ->from('branch_departments')
                                ->where('branch_id', $branchId)
                                ->where('is_active', true);
                        })
                        ->orderBy('name_ar')
                        ->pluck('name_ar', 'id');
                })
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (Set $set) => $set('job_title_id', null))
                ->rules(function (Get $get) {
                    $branchId = (int) $get('branch_id');

                    return [
                        'required',
                        'integer',
                        'exists:departments,id',
                        new DepartmentInBranch($branchId ?: null),
                    ];
                }),
            Select::make('job_title_id')
                ->label('المسمى الوظيفي')
                ->options(function (Get $get) {
                    $branchId = $get('branch_id');
                    $departmentId = $get('department_id');

                    if (! $branchId) {
                        return [];
                    }

                    return JobTitle::query()
                        ->where('job_titles.is_active', true)
                        ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
                        ->whereIn('job_titles.id', function ($query) use ($branchId) {
                            $query->select('job_title_id')
                                ->from('branch_job_titles')
                                ->where('branch_id', $branchId)
                                ->where('is_active', true);
                        })
                        ->orderBy('name_ar')
                        ->pluck('name_ar', 'id');
                })
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->rules(function (Get $get) {
                    $branchId = (int) $get('branch_id');
                    $departmentId = (int) $get('department_id');

                    $rules = [
                        'required',
                        'integer',
                        'exists:job_titles,id',
                    ];

                    if ($branchId) {
                        $rules[] = new JobTitleInBranch($branchId);
                    }

                    if ($departmentId) {
                        $rules[] = new JobTitleMatchesDepartment($departmentId);
                    }

                    return $rules;
                }),
            DatePicker::make('hire_date')
                ->label('تاريخ التعيين'),
            DatePicker::make('termination_date')
                ->label('تاريخ إنهاء الخدمة'),
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'active' => 'نشط',
                    'suspended' => 'موقوف',
                    'terminated' => 'منتهي',
                ])
                ->default('active')
                ->native(false)
                ->required(),
            Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_code')
                    ->label('الكود')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                TextColumn::make('branch.name_ar')
                    ->label('الفرع')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('department.name_ar')
                    ->label('الإدارة')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jobTitle.name_ar')
                    ->label('المسمى الوظيفي')
                    ->sortable()
                    ->toggleable(),
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'suspended',
                        'danger' => 'terminated',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-pause' => 'suspended',
                        'heroicon-o-x-circle' => 'terminated',
                    ]),
                TextColumn::make('hire_date')
                    ->label('تاريخ التعيين')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->options(Branch::query()->pluck('name_ar', 'id')),
                SelectFilter::make('department_id')
                    ->label('الإدارة')
                    ->options(Department::query()->pluck('name_ar', 'id')),
                SelectFilter::make('job_title_id')
                    ->label('المسمى الوظيفي')
                    ->options(JobTitle::query()->pluck('name_ar', 'id')),
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'suspended' => 'موقوف',
                        'terminated' => 'منتهي',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            PhonesRelationManager::class,
            IdentitiesRelationManager::class,
            LicensesRelationManager::class,
            BankAccountsRelationManager::class,
            FilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
