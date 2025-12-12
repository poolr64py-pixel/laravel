<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiTokenUsage;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiTokenController extends Controller
{

    public function index()
    {
        // Calculate statistics
        $totalTokensAllocated = Membership::where('status', 1)->sum('total_tokens');
        $totalTokensUsed = Membership::where('status', 1)->sum('used_tokens');
        $totalTokensRemaining = $totalTokensAllocated - $totalTokensUsed;
        $tokenUsagePercentage = $totalTokensAllocated > 0
            ? round(($totalTokensUsed / $totalTokensAllocated) * 100, 2)
            : 0;

        // Get recent token usage with pagination
        $recentUsage = AiTokenUsage::with(['user', 'membership'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get top token consumers
        $topConsumers = DB::table('ai_token_usage')
            ->select('user_id', DB::raw('SUM(tokens_used) as total_used'))
            ->groupBy('user_id')
            ->orderBy('total_used', 'desc')
            ->limit(10)
            ->get();

        // Add user details
        foreach ($topConsumers as $consumer) {
            $consumer->user = \App\Models\User::find($consumer->user_id);
        }

        // Token usage by action
        $usageByAction = DB::table('ai_token_usage')
            ->select('action', DB::raw('SUM(tokens_used) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->get();

        // Monthly token usage trend
        $monthlyUsage = DB::table('ai_token_usage')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(tokens_used) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.ai-tokens.index', compact(
            'totalTokensAllocated',
            'totalTokensUsed',
            'totalTokensRemaining',
            'tokenUsagePercentage',
            'recentUsage',
            'topConsumers',
            'usageByAction',
            'monthlyUsage'
        ));
    }
}
