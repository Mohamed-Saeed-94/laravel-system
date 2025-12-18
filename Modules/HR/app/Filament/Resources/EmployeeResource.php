<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    protected static ?string $navigationLabel = null;
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;

    protected static ?string $pluralLabel = null;
    protected static ?string $label = null;

    public static function getNavigationLabel(): string
    {
        // لو عندك ترجمة HR استخدم hr::employees.navigation_label
        return __('hr::employees.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('hr::groups.hr');
    }

    public static function getPluralLabel(): string
    {
        return __('hr::employees.label');
    }

    public static function getLabel(): string
    {
        return __('hr::employees.singular');
    }

    /**
     * Use Filament auth to respect panel guard (نفس ستايل Core).
     */
    protected static function authUser(): ?Model
    {
        return filament()->auth()->user();
    }

    // Permissions (غيّر أسماء الصلاحيات لو نظامك مختلف)
    public static function canViewAny(): bool
    {
        return static::authUser()?->can('employees.view_any') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return static::authUser()?->can('employees.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::authUser()?->can('employees.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::authUser()?->can('employees.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::authUser()?->can('employees.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return static::authUser()?->can('employees.delete_any') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('employee_code')
                ->label(__('hr::employees.fields.employee_code'))
                ->required()
                ->maxLength(30)
                ->unique(table: 'employees', column: 'employee_code', ignoreRecord: true),

            TextInput::make('full_name')
                ->label(__('hr::employees.fields.full_name'))
                ->required()
                ->maxLength(200),

            TextInput::make('email')
                ->label(__('hr::employees.fields.email'))
                ->nullable()
                ->email()
                ->maxLength(255)
                // يمنع إرسال "" ويحوّله null
                ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                ->unique(table: 'employees', column: 'email', ignoreRecord: true),

            Select::make('gender')
                ->label(__('hr::employees.fields.gender'))
                ->options([
                    'male' => __('hr::employees.gender.male'),
                    'female' => __('hr::employees.gender.female'),
                ])
                ->native(false)
                ->nullable(),

            Select::make('branch_id')
                ->label(__('hr::employees.fields.branch'))
                ->options(fn () => Branch::query()
                    ->where('is_active', true)
                    ->orderBy('name_ar')
                    ->pluck('name_ar', 'id'))
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ( $set) {
                    $set('department_id', null);
                    $set('job_title_id', null);
                }),

            Select::make('department_id')
                ->label(__('hr::employees.fields.department'))
                ->options(function ( $get) {
                    $branchId = (int) $get('branch_id');
                    if (! $branchId) return [];

                    return Department::query()
                        ->where('departments.is_active', true)
                        ->whereIn('departments.id', function ($q) use ($branchId) {
                            $q->select('department_id')
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
                ->live()
                ->afterStateUpdated(fn ( $set) => $set('job_title_id', null))
                ->rules(function ( $get) {
                    $branchId = (int) $get('branch_id');

                    return [
                        'required',
                        'integer',
                        'exists:departments,id',
                        // ✅ لا تفشل قبل اختيار الفرع
                        fn ($attribute, $value, $fail) => $branchId
                            ? (new DepartmentInBranch($branchId))->validate($attribute, $value, $fail)
                            : null,
                    ];
                }),

            Select::make('job_title_id')
                ->label(__('hr::employees.fields.job_title'))
                ->options(function ( $get) {
                    $branchId = (int) $get('branch_id');
                    $departmentId = (int) $get('department_id');

                    if (! $branchId) return [];

                    return JobTitle::query()
                        ->where('job_titles.is_active', true)
                        ->whereIn('job_titles.id', function ($q) use ($branchId) {
                            $q->select('job_title_id')
                                ->from('branch_job_titles')
                                ->where('branch_id', $branchId)
                                ->where('is_active', true);
                        })
                        ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                        ->orderBy('name_ar')
                        ->pluck('name_ar', 'id');
                })
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->rules(function ( $get) {
                    $branchId = (int) $get('branch_id');
                    $departmentId = (int) $get('department_id');

                    return [
                        'required',
                        'integer',
                        'exists:job_titles,id',
                        fn ($attribute, $value, $fail) => $branchId
                            ? (new JobTitleInBranch($branchId))->validate($attribute, $value, $fail)
                            : null,
                        fn ($attribute, $value, $fail) => $departmentId
                            ? (new JobTitleMatchesDepartment($departmentId))->validate($attribute, $value, $fail)
                            : null,
                    ];
                }),

            DatePicker::make('hire_date')
                ->label(__('hr::employees.fields.hire_date'))
                ->nullable(),

            DatePicker::make('termination_date')
                ->label(__('hr::employees.fields.termination_date'))
                ->nullable(),

            Select::make('status')
                ->label(__('hr::employees.fields.status'))
                ->options([
                    'active' => __('hr::employees.status.active'),
                    'suspended' => __('hr::employees.status.suspended'),
                    'terminated' => __('hr::employees.status.terminated'),
                ])
                ->default('active')
                ->native(false)
                ->required(),

            Textarea::make('notes')
                ->label(__('hr::employees.fields.notes'))
                ->columnSpanFull()
                ->nullable(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_code')
                    ->label(__('hr::employees.fields.employee_code'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label(__('hr::employees.fields.full_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('hr::employees.fields.email'))
                    ->searchable(),

                TextColumn::make('branch.name_ar')
                    ->label(__('hr::employees.fields.branch'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('department.name_ar')
                    ->label(__('hr::employees.fields.department'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jobTitle.name_ar')
                    ->label(__('hr::employees.fields.job_title'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('hr::employees.fields.status'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'suspended' => 'heroicon-o-pause',
                        'terminated' => 'heroicon-o-x-circle',
                        default => null,
                    }),

                TextColumn::make('hire_date')
                    ->label(__('hr::employees.fields.hire_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->label(__('hr::employees.fields.branch'))
                    ->options(fn () => Branch::query()->pluck('name_ar', 'id')),

                SelectFilter::make('department_id')
                    ->label(__('hr::employees.fields.department'))
                    ->options(fn () => Department::query()->pluck('name_ar', 'id')),

                SelectFilter::make('job_title_id')
                    ->label(__('hr::employees.fields.job_title'))
                    ->options(fn () => JobTitle::query()->pluck('name_ar', 'id')),

                SelectFilter::make('status')
                    ->label(__('hr::employees.fields.status'))
                    ->options([
                        'active' => __('hr::employees.status.active'),
                        'suspended' => __('hr::employees.status.suspended'),
                        'terminated' => __('hr::employees.status.terminated'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn (Model $record): bool => static::canView($record)),

                EditAction::make()
                    ->visible(fn (Model $record): bool => static::canEdit($record)),

                \Filament\Actions\DeleteAction::make()
                    ->visible(fn (Model $record): bool => static::canDelete($record)),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->visible(fn (): bool => static::canCreate()),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => static::canDeleteAny()),
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
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit'   => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
