<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Orders by Status';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [
                        Order::where('status', 'pending')->count(),
                        Order::where('status', 'confirmed')->count(),
                        Order::where('status', 'delivered')->count(),
                        Order::where('status', 'cancelled')->count(),
                    ],
                    'backgroundColor' => [
                        'rgba(234, 179, 8, 0.8)',   // yellow - pending
                        'rgba(59, 130, 246, 0.8)',  // blue - confirmed
                        'rgba(16, 185, 129, 0.8)',  // green - delivered
                        'rgba(239, 68, 68, 0.8)',   // red - cancelled
                    ],
                    'borderColor' => [
                        'rgb(234, 179, 8)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Pending', 'Confirmed', 'Delivered', 'Cancelled'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
