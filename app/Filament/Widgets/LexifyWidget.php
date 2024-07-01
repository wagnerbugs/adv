<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LexifyWidget extends Widget
{
    protected int | string | array $columnSpan = '2';

    protected static string $view = 'filament.widgets.lexify-widget';
}
