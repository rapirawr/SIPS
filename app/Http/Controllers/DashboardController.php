<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\KategoriPengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get time period filter (default: last 30 days)
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays((int)$period)->startOfDay();

        // Basic statistics
        $stats = $this->getBasicStats($user);

        // Chart data
        $trendData = $this->getTrendData($startDate);
        $kategoriData = $this->getKategoriData($startDate);
        $statusData = $this->getStatusData();

        // Recent pengaduan
        $recentPengaduan = $this->getRecentPengaduan($user);

        // Urgent pengaduan
        $urgentPengaduan = Pengaduan::urgent()
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->with(['kategori', 'assignedUser'])
            ->latest()
            ->limit(5)
            ->get();

        // Overdue SLA
        $overduePengaduan = Pengaduan::overdueSla()
            ->with(['kategori', 'assignedUser'])
            ->latest()
            ->limit(5)
            ->get();

        // Top categories
        $topKategoris = KategoriPengaduan::withCount(['pengaduan' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderBy('pengaduan_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'trendData',
            'kategoriData',
            'statusData',
            'recentPengaduan',
            'urgentPengaduan',
            'overduePengaduan',
            'topKategoris'
        ));
    }

    /**
     * Get basic statistics
     */
    private function getBasicStats($user)
    {
        $stats = [
            'total' => Pengaduan::count(),
            'pending' => Pengaduan::pending()->count(),
            'verified' => Pengaduan::verified()->count(),
            'in_progress' => Pengaduan::inProgress()->count(),
            'resolved' => Pengaduan::resolved()->count(),
            'rejected' => Pengaduan::rejected()->count(),
            'urgent' => Pengaduan::urgent()->whereNotIn('status', ['resolved', 'rejected'])->count(),
            'overdue_sla' => Pengaduan::overdueSla()->count(),
        ];

        // User-specific stats
        if ($user->isPetugas()) {
            $stats['my_assigned'] = Pengaduan::assignedTo($user->id)
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->count();
            
            $stats['my_pending'] = Pengaduan::assignedTo($user->id)
                ->pending()
                ->count();
        }

        if (!$user->isAdmin() && !$user->isPetugas()) {
            $stats['my_pengaduan'] = Pengaduan::where('user_id', $user->id)->count();
            $stats['my_resolved'] = Pengaduan::where('user_id', $user->id)->resolved()->count();
        }

        // Calculate resolution rate
        $totalResolved = Pengaduan::resolved()->count();
        $totalFinal = Pengaduan::whereIn('status', ['resolved', 'rejected'])->count();
        $stats['resolution_rate'] = $totalFinal > 0 ? round(($totalResolved / $totalFinal) * 100, 1) : 0;

        // Average resolution time (in hours)
        $avgResolutionTime = Pengaduan::resolved()
            ->whereNotNull('resolved_at')
            ->get()
            ->avg(function ($pengaduan) {
                return $pengaduan->created_at->diffInHours($pengaduan->resolved_at);
            });
        
        $stats['avg_resolution_hours'] = $avgResolutionTime ? round($avgResolutionTime, 1) : 0;

        return $stats;
    }

    /**
     * Get trend data for chart
     */
    private function getTrendData($startDate)
    {
        $data = Pengaduan::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'))->toArray(),
            'total' => $data->pluck('total')->toArray(),
            'resolved' => $data->pluck('resolved')->toArray(),
            'pending' => $data->pluck('pending')->toArray(),
        ];
    }

    /**
     * Get kategori distribution data
     */
    private function getKategoriData($startDate)
    {
        $data = KategoriPengaduan::withCount(['pengaduan' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->having('pengaduan_count', '>', 0)
            ->get();

        return [
            'labels' => $data->pluck('nama')->toArray(),
            'values' => $data->pluck('pengaduan_count')->toArray(),
            'colors' => $data->pluck('warna')->toArray(),
        ];
    }

    /**
     * Get status distribution data
     */
    private function getStatusData()
    {
        $statusLabels = [
            'pending' => 'Pending',
            'verified' => 'Terverifikasi',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
        ];

        $data = Pengaduan::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $data->pluck('status')->map(fn($status) => $statusLabels[$status] ?? $status)->toArray(),
            'values' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get recent pengaduan based on user role
     */
    private function getRecentPengaduan($user)
    {
        $query = Pengaduan::with(['kategori', 'user', 'assignedUser']);

        if ($user->isPetugas() && !$user->isAdmin()) {
            // Petugas only see assigned pengaduan
            $query->assignedTo($user->id);
        } elseif (!$user->isPetugas() && !$user->isAdmin()) {
            // Regular user only see their own pengaduan
            $query->where('user_id', $user->id);
        }

        return $query->latest()->limit(10)->get();
    }

    /**
     * Get analytics data (API endpoint)
     */
    public function analytics(Request $request)
    {
        if (!$request->wantsJson() && !$request->ajax()) {
            return view('dashboard.analytics');
        }
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays((int)$period)->startOfDay();

        $data = [
            'trend' => $this->getTrendData($startDate),
            'kategori' => $this->getKategoriData($startDate),
            'status' => $this->getStatusData(),
            'stats' => $this->getBasicStats(auth()->user()),
        ];

        return response()->json($data);
    }

    /**
     * Get monthly report data
     */
    public function monthlyReport(Request $request)
    {
        if (!$request->wantsJson() && !$request->ajax()) {
            return view('dashboard.monthly-report');
        }
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $report = [
            'period' => $startDate->format('F Y'),
            'total_received' => Pengaduan::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_resolved' => Pengaduan::resolved()->whereBetween('resolved_at', [$startDate, $endDate])->count(),
            'by_kategori' => KategoriPengaduan::withCount(['pengaduan' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->get(),
            'by_status' => Pengaduan::select('status', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('status')
                ->get(),
            'avg_resolution_time' => Pengaduan::resolved()
                ->whereBetween('resolved_at', [$startDate, $endDate])
                ->get()
                ->avg(function ($p) {
                    return $p->created_at->diffInHours($p->resolved_at);
                }),
        ];

        return response()->json($report);
    }
}