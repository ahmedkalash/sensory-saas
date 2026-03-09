<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardHeroWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-hero-widget';

    protected int|string|array $columnSpan = 'full';
}
