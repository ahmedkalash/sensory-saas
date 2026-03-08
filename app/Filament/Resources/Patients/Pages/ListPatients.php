<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Enums\PatientStatus;
use App\Filament\Resources\Patients\PatientResource;
use App\Models\Patient;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->badge(Patient::query()->count()),
            'active' => Tab::make('نشط')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PatientStatus::ACTIVE))
                ->badge(Patient::query()->where('status', PatientStatus::ACTIVE)->count()),
            'completed' => Tab::make('تم إنهاء العلاج')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PatientStatus::COMPLETED))
                ->badge(Patient::query()->where('status', PatientStatus::COMPLETED)->count()),
            'archived' => Tab::make('مؤرشف')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', PatientStatus::ARCHIVED))
                ->badge(Patient::query()->where('status', PatientStatus::ARCHIVED)->count()),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'active';
    }
}
