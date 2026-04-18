<?php

namespace App\Filament\Resources\Plans\Schemas;

use App\Enums\PlanType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الخطة')
                    ->required()
                    ->maxLength(255),

                TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->prefix('ج.م')
                    ->required()
                    ->default(0),

                Select::make('type')
                    ->label('نوع الخطة')
                    ->options(PlanType::class)
                    ->required()
                    ->live(),

                TextInput::make('duration_days')
                    ->label('المدة بالأيام (مطلوب للسنوي)')
                    ->numeric()
                    ->required(fn (callable $get) => $get('type') === PlanType::Yearly->value)
                    ->visible(fn (callable $get) => $get('type') === PlanType::Yearly->value),

                TextInput::make('quota_count')
                    ->label('عدد التقييمات (مطلوب للحصص)')
                    ->numeric()
                    ->required(fn (callable $get) => $get('type') === PlanType::Quota->value)
                    ->visible(fn (callable $get) => $get('type') === PlanType::Quota->value),

                Textarea::make('description')
                    ->label('الوصف')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('مُفعل')
                    ->default(true),
            ]);
    }
}
