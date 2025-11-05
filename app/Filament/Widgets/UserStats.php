<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;

class UserStats extends ChartWidget
{
    protected static ?string $heading = '📈 Реєстрації користувачів';

    protected function getData(): array
    {
        $registrations = User::query()
            ->selectRaw('COUNT(*) as count, DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Користувачі',
                    'data' => $registrations->pluck('count')->toArray(),
                    'borderColor' => '#e63946',
                    'backgroundColor' => 'rgba(230, 57, 70, 0.2)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $registrations->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
