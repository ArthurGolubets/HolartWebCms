<?php

namespace HolartWeb\HolartCMS\Services;

use HolartWeb\HolartCMS\Models\TAdministrator;
use HolartWeb\HolartCMS\Models\TAdminAction;
use HolartWeb\HolartCMS\Models\Pages\TPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardMetricsService
{
    /**
     * Get users statistics
     */
    public function getUsersStats(): array
    {
        return [
            'total' => TAdministrator::count(),
            'active' => TAdministrator::where('is_active', true)->count(),
            'inactive' => TAdministrator::where('is_active', false)->count(),
        ];
    }

    /**
     * Get popular pages by views
     */
    public function getPopularPages(int $limit = 10): array
    {
        // Check if pages table exists
        if (!Schema::hasTable('t_pages')) {
            return [];
        }

        try {
            // This would require a page_views table or analytics integration
            // For now, return pages ordered by updated_at as a placeholder
            return \Illuminate\Support\Facades\DB::table('t_pages')
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get(['id', 'title', 'slug', 'updated_at'])
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'views' => rand(10, 1000), // Placeholder - should be from analytics
                        'last_update' => date('d.m.Y H:i', strtotime($page->updated_at)),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get recent admin actions (logs)
     */
    public function getRecentLogs(int $limit = 6): array
    {
        if (!Schema::hasTable('t_admin_actions')) {
            return [];
        }

        return TAdminAction::with('admin:id,name,email')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'admin_name' => $log->admin->name ?? 'Unknown',
                    'action' => $log->action,
                    'entity_type' => $log->entity_type,
                    'entity_id' => $log->entity_id,
                    'description' => $log->description,
                    'created_at' => $log->created_at->format('d.m.Y H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Get orders statistics
     */
    public function getOrdersStats(): array
    {
        if (!Schema::hasTable('t_orders')) {
            return [];
        }

        try {
            return [
                'total' => DB::table('t_orders')->count(),
                'new' => DB::table('t_orders')->where('delivery_status', 'pending')->count(),
                'revenue' => (float) DB::table('t_orders')->sum('total_price'),
                'pending_payment' => (float) DB::table('t_orders')->where('payment_status', 'pending')->sum('total_price'),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(int $limit = 5): array
    {
        if (!Schema::hasTable('t_orders')) {
            return [];
        }

        try {
            return DB::table('t_orders')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'name' => $order->name,
                        'email' => $order->email,
                        'phone' => $order->phone,
                        'total_price' => $order->total_price,
                        'payment_status' => $order->payment_status,
                        'delivery_status' => $order->delivery_status,
                        'created_at' => date('d.m.Y H:i', strtotime($order->created_at)),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get promocodes usage statistics
     */
    public function getPromocodesUsage(int $limit = 10): array
    {
        if (!Schema::hasTable('t_promocodes')) {
            return [];
        }

        try {
            return DB::table('t_promocodes')
                ->orderBy('current_usage', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($promo) {
                    return [
                        'id' => $promo->id,
                        'name' => $promo->name,
                        'code' => $promo->code,
                        'current_usage' => $promo->current_usage ?? 0,
                        'max_usage' => $promo->max_usage ?? 0,
                        'value' => $promo->value,
                        'type' => $promo->type,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get subscriptions count
     */
    public function getSubscriptionsCount(): array
    {
        if (!Schema::hasTable('t_users_emails')) {
            return [];
        }

        try {
            return [
                'total' => DB::table('t_users_emails')->count(),
                'active' => DB::table('t_users_emails')->where('status', 'active')->count(),
                'unsubscribed' => DB::table('t_users_emails')->where('status', 'unsubscribed')->count(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get subscriptions chart data (last 30 days)
     */
    public function getSubscriptionsChart(int $days = 30): array
    {
        if (!Schema::hasTable('t_users_emails')) {
            return [];
        }

        try {
            $data = [];
            $startDate = now()->subDays($days - 1); // Include today

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i);
                $count = DB::table('t_users_emails')
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->count();

                $data[] = [
                    'label' => $date->format('d.m'),
                    'value' => $count,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get reviews count
     */
    public function getReviewsCount(): array
    {
        if (!Schema::hasTable('t_comments')) {
            return [];
        }

        try {
            return [
                'total' => DB::table('t_comments')->count(),
                'moderated' => DB::table('t_comments')->where('is_moderated', true)->count(),
                'pending' => DB::table('t_comments')->where('is_moderated', false)->count(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get user requests count
     */
    public function getRequestsCount(): array
    {
        if (!Schema::hasTable('t_user_requests')) {
            return [];
        }

        try {
            return [
                'total' => DB::table('t_user_requests')->count(),
                'today' => DB::table('t_user_requests')->whereDate('created_at', today())->count(),
                'week' => DB::table('t_user_requests')->where('created_at', '>=', now()->subWeek())->count(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get recent user requests
     */
    public function getRecentRequests(int $limit = 5): array
    {
        if (!Schema::hasTable('t_user_requests')) {
            return [];
        }

        try {
            return DB::table('t_user_requests')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'comment' => $request->comment,
                        'created_at' => date('d.m.Y H:i', strtotime($request->created_at)),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get orders chart data (30 days)
     */
    public function getOrdersChart(int $days = 30): array
    {
        if (!Schema::hasTable('t_orders')) {
            return [];
        }

        try {
            $data = [];
            $startDate = now()->subDays($days - 1); // Include today

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i);
                $count = DB::table('t_orders')
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->count();

                $data[] = [
                    'label' => $date->format('d.m'),
                    'value' => $count,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get revenue chart data (30 days)
     */
    public function getRevenueChart(int $days = 30): array
    {
        if (!Schema::hasTable('t_orders')) {
            return [];
        }

        try {
            $data = [];
            $startDate = now()->subDays($days - 1); // Include today

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i);
                $revenue = (float) DB::table('t_orders')
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->sum('total_price');

                $data[] = [
                    'label' => $date->format('d.m'),
                    'value' => $revenue,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get pages views chart (top 10)
     */
    public function getPagesViewsChart(int $limit = 10): array
    {
        if (!Schema::hasTable('t_pages')) {
            return [];
        }

        try {
            return DB::table('t_pages')
                ->where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get(['id', 'title'])
                ->map(function ($page) {
                    return [
                        'label' => $page->title,
                        'value' => rand(50, 500), // Placeholder - should be from analytics
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all metrics for dashboard
     */
    public function getAllMetrics(): array
    {
        $metrics = [
            'users_stats' => $this->getUsersStats(),
            'popular_pages' => $this->getPopularPages(),
            'recent_logs' => $this->getRecentLogs(),
            'pages_views_chart' => $this->getPagesViewsChart(),
        ];

        // Add commerce metrics if module is installed
        if (Schema::hasTable('t_orders')) {
            $metrics['orders_stats'] = $this->getOrdersStats();
            $metrics['recent_orders'] = $this->getRecentOrders();
            $metrics['promocodes_usage'] = $this->getPromocodesUsage();
            $metrics['orders_chart'] = $this->getOrdersChart();
            $metrics['revenue_chart'] = $this->getRevenueChart();
        }

        // Add callback metrics if module is installed
        if (Schema::hasTable('t_users_emails')) {
            $metrics['subscriptions_count'] = $this->getSubscriptionsCount();
            $metrics['subscriptions_chart'] = $this->getSubscriptionsChart();
            $metrics['reviews_count'] = $this->getReviewsCount();
            $metrics['requests_count'] = $this->getRequestsCount();
            $metrics['recent_requests'] = $this->getRecentRequests();
        }

        return $metrics;
    }

    /**
     * Get metric by type
     */
    public function getMetricByType(string $type): mixed
    {
        return match ($type) {
            'users_stats' => $this->getUsersStats(),
            'popular_pages' => $this->getPopularPages(),
            'recent_logs' => $this->getRecentLogs(),
            'pages_views_chart' => $this->getPagesViewsChart(),
            'orders_stats' => $this->getOrdersStats(),
            'recent_orders' => $this->getRecentOrders(),
            'promocodes_usage' => $this->getPromocodesUsage(),
            'orders_chart' => $this->getOrdersChart(),
            'revenue_chart' => $this->getRevenueChart(),
            'subscriptions_count' => $this->getSubscriptionsCount(),
            'subscriptions_chart' => $this->getSubscriptionsChart(),
            'reviews_count' => $this->getReviewsCount(),
            'requests_count' => $this->getRequestsCount(),
            'recent_requests' => $this->getRecentRequests(),
            default => [],
        };
    }
}
