<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $title = 'الاشتراكات';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('plan_id')
                    ->label('الخطة')
                    ->relationship('plan', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, ?string $state) {
                        if (! $state) {
                            return;
                        }
                        $plan = \App\Models\Plan::find($state);
                        if ($plan) {
                            if ($plan->isYearly()) {
                                $set('ends_at', now()->addDays($plan->duration_days)->format('Y-m-d'));
                                $set('quota_remaining', null);
                            } else {
                                $set('ends_at', null);
                                $set('quota_remaining', $plan->quota_count);
                            }
                        }
                    }),

                Toggle::make('is_suspended')
                    ->label('موقوف مؤقتاً')
                    ->onIcon('heroicon-m-pause')
                    ->offIcon('heroicon-m-play')
                    ->onColor('warning'),

                DatePicker::make('ends_at')
                    ->label('تاريخ الانتهاء')
                    ->visible(fn (callable $get) => filled($get('ends_at')) || (\App\Models\Plan::find($get('plan_id'))?->isYearly() ?? false)),

                TextInput::make('quota_remaining')
                    ->label('الرصيد المتبقي')
                    ->numeric()
                    ->visible(fn (callable $get) => filled($get('quota_remaining')) || (\App\Models\Plan::find($get('plan_id'))?->isQuota() ?? false)),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan.name')
                    ->label('الخطة')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('تاريخ الانتهاء')
                    ->date()
                    ->badge()
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? Color::Red : Color::Green)
                    ->sortable(),

                TextColumn::make('quota_remaining')
                    ->label('الرصيد')
                    ->numeric(locale: 'en')
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'expired' => 'منتهي',
                        'suspended' => 'موقوف',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'active' => $query->active(),
                            'expired' => $query->expired(),
                            'suspended' => $query->suspended(),
                            default => $query,
                        };
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة اشتراك'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
