<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Models\Dimension;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تفاصيل السؤال')
                    ->schema([
                        Select::make('dimension_id')
                            ->label('البُعد / المقياس')
                            ->options(
                                Dimension::with('measurement')
                                    ->get()
                                    ->mapWithKeys(fn (Dimension $d) => [
                                        $d->id => "{$d->measurement->name} ← {$d->name}",
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        Textarea::make('q_text')
                            ->label('نص السؤال')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('التوصيات والأنشطة والأهداف')
                    ->description('يتم تضمين هذه البنود في التقرير لكل سؤال يحصل على درجة ٢ أو ٣.')
                    ->schema([
                        Repeater::make('recommendations')
                            ->label('التوصيات')
                            ->schema([
                                TextInput::make('value')
                                    ->label('توصية')
                                    ->required(),
                            ])
                            ->addActionLabel('إضافة توصية')
                            ->simple(TextInput::make('value')->label('توصية'))
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Repeater::make('activities')
                            ->label('الأنشطة')
                            ->simple(TextInput::make('value')->label('نشاط'))
                            ->addActionLabel('إضافة نشاط')
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Repeater::make('goals')
                            ->label('الأهداف')
                            ->simple(TextInput::make('value')->label('هدف'))
                            ->addActionLabel('إضافة هدف')
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
