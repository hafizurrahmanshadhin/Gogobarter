<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller {
    /**
     * Display the dashboard page.
     *
     * @return View
     */
    public function index(): View {
        // Get statistics
        $stats = $this->getStatistics();

        // Get recent data
        $recentUsers         = User::where('role', '!=', 'admin')->latest()->take(5)->get();
        $recentProducts      = Product::with('user', 'category')->latest()->take(5)->get();
        $recentSubscriptions = UserSubscription::with('user', 'subscriptionPlan')->latest()->take(5)->get();

        // Get system info
        $systemInfo = SystemSetting::first();

        // Get chart data
        $chartData = $this->getChartData();

        return view('backend.layouts.dashboard.index', compact(
            'stats',
            'recentUsers',
            'recentProducts',
            'recentSubscriptions',
            'systemInfo',
            'chartData'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics(): array {
        $totalUsers          = User::where('role', '!=', 'admin')->count();
        $totalProducts       = Product::count();
        $totalPlans          = SubscriptionPlan::count();
        $activeSubscriptions = UserSubscription::where('status', 'active')->count();

        // Get growth percentages
        $usersGrowth         = $this->getGrowthPercentage(User::class);
        $productsGrowth      = $this->getGrowthPercentage(Product::class);
        $subscriptionsGrowth = $this->getGrowthPercentage(UserSubscription::class, 'active');

        return [
            'users'         => [
                'total'  => $totalUsers,
                'growth' => $usersGrowth,
                'icon'   => 'bi-people',
                'color'  => 'primary',
            ],
            'products'      => [
                'total'  => $totalProducts,
                'growth' => $productsGrowth,
                'icon'   => 'bi-box-seam',
                'color'  => 'success',
            ],
            'plans'         => [
                'total'  => $totalPlans,
                'growth' => 0,
                'icon'   => 'bi-card-list',
                'color'  => 'info',
            ],
            'subscriptions' => [
                'total'  => $activeSubscriptions,
                'growth' => $subscriptionsGrowth,
                'icon'   => 'bi-person-badge',
                'color'  => 'warning',
            ],
        ];
    }

    /**
     * Calculate growth percentage compared to last month
     */
    private function getGrowthPercentage(string $model, string $status = null): float {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth    = Carbon::now()->subMonth()->startOfMonth();

        $query = $model::query();

        if ($status) {
            $currentCount = $query->where('status', $status)
                ->where('created_at', '>=', $currentMonth)->count();
            $lastCount = $query->where('status', $status)
                ->whereBetween('created_at', [$lastMonth, $currentMonth])->count();
        } else {
            $currentCount = $query->where('created_at', '>=', $currentMonth)->count();
            $lastCount    = $query->whereBetween('created_at', [$lastMonth, $currentMonth])->count();
        }

        if ($lastCount == 0) {
            return $currentCount > 0 ? 100 : 0;
        }

        return round((($currentCount - $lastCount) / $lastCount) * 100, 1);
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData(): array {
        $last7Days = collect(range(6, 0))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('Y-m-d');
        });

        $userRegistrations = $last7Days->map(function ($date) {
            return User::whereDate('created_at', $date)->count();
        });

        $productCreations = $last7Days->map(function ($date) {
            return Product::whereDate('created_at', $date)->count();
        });

        return [
            'labels'   => $last7Days->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'users'    => $userRegistrations->toArray(),
            'products' => $productCreations->toArray(),
        ];
    }
}
