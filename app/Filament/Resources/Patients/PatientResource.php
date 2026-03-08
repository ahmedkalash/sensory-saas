<?php

namespace App\Filament\Resources\Patients;

use App\Filament\Resources\Patients\Pages\EditPatient;
use App\Filament\Resources\Patients\Pages\ManagePatients;
use App\Models\Patient;
use BackedEnum;
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
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'الأطفال';

    protected static ?string $modelLabel = 'طفل';

    protected static ?string $pluralModelLabel = 'الأطفال';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الطفل')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('dob')
                    ->label('تاريخ الميلاد')
                    ->required()
                    ->maxDate(now()),
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
                RichEditor::make('medical_plan')
                    ->label('الخطة العلاجية/الطبية')
                    ->default('<p>الخطة الشهرية</p><table><tbody><tr><th rowspan="1" colspan="1"><p>الهدف</p></th><th rowspan="1" colspan="1" data-colwidth="398"><p>الإستجابة</p></th><th rowspan="1" colspan="1" data-colwidth="342"><p>المتبقي</p></th></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr><tr><td rowspan="1" colspan="1"><p></p></td><td rowspan="1" colspan="1" data-colwidth="398"><p></p></td><td rowspan="1" colspan="1" data-colwidth="342"><p></p></td></tr></tbody></table><p></p><p>نسبة النجاح المحققة للمجال ككل = ... %</p><p></p>')
                    ->columnSpanFull(),
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
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
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
            'index' => ManagePatients::route('/'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
