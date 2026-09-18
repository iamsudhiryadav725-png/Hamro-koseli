<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s'; // ← non-static

    protected function getStats(): array
    {
        $ordersPerDay = collect(range(6, 0))->map(
            fn ($i) => Order::whereDate('created_at', Carbon::now()->subDays($i))->count()
        )->toArray();

        $revenuePerDay = collect(range(6, 0))->map(
            fn ($i) => Payment::completed()->whereDate('paid_at', Carbon::now()->subDays($i))->sum('total_amount')
        )->toArray();

        $usersPerDay = collect(range(6, 0))->map(
            fn ($i) => User::whereDate('created_at', Carbon::now()->subDays($i))->count()
        )->toArray();

        return [
            Stat::make('Total Users', User::where('role', 'user')->count())
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->chart($usersPerDay)
                ->color('primary'),

            Stat::make('Total Vendors', Vendor::count())
                ->description('Active: '.Vendor::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning'),

            Stat::make('Total Products', Product::count())
                ->description('Active: '.Product::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Total Orders', Order::count())
                ->description('Pending: '.Order::pending()->count())
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart($ordersPerDay)
                ->color('info'),

            Stat::make('Total Revenue', 'Rs. '.number_format(Payment::completed()->sum('total_amount'), 2))
                ->description('This month: Rs. '.number_format(
                    Payment::completed()->whereMonth('paid_at', Carbon::now()->month)->sum('total_amount'), 2
                ))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($revenuePerDay)
                ->color('success'),

            Stat::make('Pending Orders', Order::pending()->count())
                ->description('Delivered: '.Order::delivered()->count())
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
