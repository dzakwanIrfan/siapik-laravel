<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get dashboard data based on user role
        $dashboardData = $this->getDashboardData($user);
        
        return view('dashboard', compact('dashboardData'));
    }

    public function getStats()
    {
        $user = Auth::user();
        $stats = $this->getDashboardData($user);
        
        return response()->json($stats);
    }

    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'status');
        
        $chartData = $this->getChartDataByType($user, $type);
        
        return response()->json($chartData);
    }

    public function getRecentSubmissions()
    {
        $user = Auth::user();
        $submissions = $this->getSubmissionsQuery($user)
            ->with(['letterType', 'user.mahasiswaProfile.major'])
            ->orderBy('dtmInserted', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $submissions->map(function ($submission) {
                return [
                    'id' => $submission->intSubmission_ID,
                    'receipt_number' => $submission->txtReceiptNumber,
                    'letter_type' => $submission->letterType->txtNameLetterType ?? '-',
                    'user_name' => $submission->user->txtFullName,
                    'major' => $submission->user->mahasiswaProfile->major->txtNameMajor ?? '-',
                    'status' => $submission->txtStatus,
                    'created_at' => $submission->dtmInserted->format('d/m/Y H:i'),
                    'url' => route('submissions.receipt', $submission->intSubmission_ID)
                ];
            })
        ]);
    }

    private function getDashboardData($user)
    {
        // Status categories
        $pendingStatuses = ['Sedang ditinjau Kaprodi'];
        $onProgressStatuses = ['Disetujui Kaprodi', 'Disetujui Akademik', 'Sudah dicetak'];
        $closedStatuses = ['Ditolak Kaprodi', 'Ditolak Akademik', 'Selesai'];
        
        // Get fresh query for each count to avoid conflicts
        $totalCount = $this->getSubmissionsQuery($user)->count();
        $pendingCount = $this->getSubmissionsQuery($user)->whereIn('txtStatus', $pendingStatuses)->count();
        $onProgressCount = $this->getSubmissionsQuery($user)->whereIn('txtStatus', $onProgressStatuses)->count();
        $closedCount = $this->getSubmissionsQuery($user)->whereIn('txtStatus', $closedStatuses)->count();
        
        $stats = [
            'total_submissions' => $totalCount,
            'pending' => $pendingCount,
            'on_progress' => $onProgressCount,
            'closed' => $closedCount,
        ];
        
        // Additional stats based on role
        if ($user->hasRole('akademik')) {
            $stats['total_users'] = User::where('bitActive', 1)->count();
            $stats['total_mahasiswa'] = User::whereHas('mahasiswaProfile')->where('bitActive', 1)->count();
            $stats['total_dosen'] = User::whereHas('dosenProfile')->where('bitActive', 1)->count();
        }
        
        return $stats;
    }

    private function getSubmissionsQuery($user)
    {
        $query = Submission::where('bitActive', 1);
        
        if ($user->hasRole('mahasiswa') || $user->hasRole('dosen')) {
            // Mahasiswa/Dosen can only see their own submissions
            $query->where('intUser_ID', $user->intUser_ID);
        } elseif ($user->hasRole('kaprodi')) {
            // Kaprodi can see submissions from their major
            $kaprodiMajorId = $user->dosenProfile->intMajor_ID ?? null;
            if ($kaprodiMajorId) {
                $query->whereHas('user.mahasiswaProfile', function ($q) use ($kaprodiMajorId) {
                    $q->where('intMajor_ID', $kaprodiMajorId);
                });
            } else {
                // If kaprodi doesn't have major, show no submissions
                $query->where('intSubmission_ID', 0);
            }
        } elseif ($user->hasRole('akademik')) {
            // Akademik can see all submissions - no additional filter needed
        } else {
            // For any other role, show no submissions
            $query->where('intSubmission_ID', 0);
        }
        
        return $query;
    }

    private function getChartDataByType($user, $type)
    {
        switch ($type) {
            case 'status':
                return $this->getStatusChartData($user);
            case 'monthly':
                return $this->getMonthlyChartData($user);
            case 'letter_type':
                return $this->getLetterTypeChartData($user);
            default:
                return $this->getStatusChartData($user);
        }
    }

    private function getStatusChartData($user)
    {
        $statusGroups = [
            'Pending' => ['Sedang ditinjau Kaprodi'],
            'On Progress' => ['Disetujui Kaprodi', 'Disetujui Akademik', 'Sudah dicetak'],
            'Closed' => ['Ditolak Kaprodi', 'Ditolak Akademik', 'Selesai']
        ];
        
        $data = [];
        $labels = [];
        
        foreach ($statusGroups as $groupName => $statuses) {
            $count = $this->getSubmissionsQuery($user)->whereIn('txtStatus', $statuses)->count();
            $labels[] = $groupName;
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'series' => $data
        ];
    }

    private function getMonthlyChartData($user)
    {
        $baseQuery = $this->getSubmissionsQuery($user);
        
        // Get last 6 months data
        $monthlyData = $baseQuery
            ->select(
                DB::raw('MONTH(dtmInserted) as month'),
                DB::raw('YEAR(dtmInserted) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('dtmInserted', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Create labels for last 6 months
        $labels = [];
        $data = [];
        
        // Generate last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            // Find count for this month
            $monthCount = $monthlyData->where('year', $date->year)
                                   ->where('month', $date->month)
                                   ->first();
            
            $data[] = $monthCount ? (int) $monthCount->count : 0;
        }
        
        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Pengajuan',
                    'data' => $data
                ]
            ]
        ];
    }

    private function getLetterTypeChartData($user)
    {
        $baseQuery = $this->getSubmissionsQuery($user);
        
        $letterTypeData = $baseQuery
            ->join('letter_types', 'submissions.intLetterType_ID', '=', 'letter_types.intLetterType_ID')
            ->select('letter_types.txtNameLetterType', DB::raw('COUNT(*) as count'))
            ->groupBy('letter_types.intLetterType_ID', 'letter_types.txtNameLetterType')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $labels = $letterTypeData->pluck('txtNameLetterType')->toArray();
        $data = $letterTypeData->pluck('count')->map(function($count) {
            return (int) $count;
        })->toArray();
        
        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Jumlah Pengajuan',
                    'data' => $data
                ]
            ]
        ];
    }
}