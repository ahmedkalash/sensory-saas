<?php

namespace App\Filament\Resources\Patients;

use App\Enums\PatientStatus;
use App\Models\Patient;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'الأطفال';

    protected static ?string $modelLabel = 'طفل';

    protected static ?string $pluralModelLabel = 'الأطفال';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('المعلومات الأساسية')->schema([
                            TextInput::make('name')
                                ->label('اسم الطفل')
                                ->required()
                                ->maxLength(255),
                            DatePicker::make('dob')
                                ->label('تاريخ الميلاد')
                                ->required()
                                ->maxDate(now()),
                            Select::make('status')
                                ->label('حالة الطفل')
                                ->options(PatientStatus::class)
                                ->default(PatientStatus::ACTIVE)
                                ->required(),
                            Select::make('gender')
                                ->label('الجنس')
                                ->options([
                                    'ذكر' => 'ذكر',
                                    'أنثى' => 'أنثى',
                                ])
                                ->required(),
                            TextInput::make('school')
                                ->label('المدرسة/المركز')
                                ->maxLength(255),
                            TextInput::make('grade')
                                ->label('الصف الدراسي')
                                ->maxLength(255),
                        ])->columns(1),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('مؤشر التقدم')
                            ->schema([
                                Livewire::make(\App\Filament\Resources\Patients\Widgets\PatientProgressChart::class),
                            ]),
                    ]),

                Section::make('الخطة العلاجية/الطبية')
                    ->columnSpanFull()
                    ->schema([
                        RichEditor::make('medical_plan')
                            ->label('')
                            ->default('<p>الخطة الشهرية</p><table><tbody><tr><th rowspan="1" colspan="1"><p>الهدف</p></th><th rowspan="1" colspan="1" data-colwidth="398"><p>الإستجابة</p></th><th rowspan="1" colspan="1" data-colwidth="342"><p>المتبقي</p></th></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr></tbody></table><p></p><p>نسبة النجاح المحققة للمجال ككل = ... %</p><p></p>')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الطفل')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->action(
                        Action::make('updateStatus')
                            ->schema([
                                Select::make('status')
                                    ->label('حالة الطفل')
                                    ->options(PatientStatus::class)
                                    ->required(),
                            ])
                            ->action(function (Patient $record, array $data): void {
                                $record->update(['status' => $data['status']]);
                            }),
                    ),
                TextColumn::make('dob')
                    ->label('تاريخ الميلاد')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('الجنس'),
                TextColumn::make('school')
                    ->label('المدرسة/المركز')
                    ->searchable(),
                TextColumn::make('grade')
                    ->label('الصف الدراسي')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('compare_evaluations')
                        ->label('مقارنة التقييمات')
                        ->icon('heroicon-o-chart-bar-square')
                        ->color('info')
                        ->schema([
                            Select::make('eval_1')
                                ->label('التقييم الأول (الأساس)')
                                ->options(fn (Patient $record) => $record->evaluations()->pluck('title', 'id'))
                                ->required(),
                            Select::make('eval_2')
                                ->label('التقييم الثاني (المتابعة)')
                                ->options(fn (Patient $record) => $record->evaluations()->pluck('title', 'id'))
                                ->required()
                                ->different('eval_1'),
                        ])
                        ->action(function (array $data, Patient $record) {
                            return redirect()->route('reports.progress', [
                                'patient' => $record->id,
                                'eval_1' => $data['eval_1'],
                                'eval_2' => $data['eval_2'],
                            ]);
                        })
                        ->disabled(fn (Patient $record) => $record->evaluations()->count() < 2),
                    DeleteAction::make(),
                ])
                    ->tooltip('الإجراءات')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray'),
            ])->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
