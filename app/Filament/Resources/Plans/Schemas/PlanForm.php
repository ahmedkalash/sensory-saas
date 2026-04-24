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
                    ->required(fn ($get) => $get('type') == PlanType::Yearly)
                    ->visible(fn ($get) => $get('type') == PlanType::Yearly),

                TextInput::make('quota_count')
                    ->label('عدد التقييمات (مطلوب للحصص)')
                    ->numeric()
                    ->required(fn ($get) => $get('type') == PlanType::Quota)
                    ->visible(fn ($get) => $get('type') == PlanType::Quota),

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
