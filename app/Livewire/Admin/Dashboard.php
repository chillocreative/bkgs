<?php

namespace App\Livewire\Admin;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $collectedThisMonth = (float) Payment::query()
            ->where('status', 'successful')
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->sum('amount');

        $invoicesThisMonth = Invoice::query()
            ->whereBetween('period_month', [$monthStart, $monthEnd])
            ->get();

        $totalIssued = (float) $invoicesThisMonth->sum('total');
        $totalPaid = (float) $invoicesThisMonth->where('status', InvoiceStatus::Paid)->sum('total');
        $outstanding = max(0, $totalIssued - $totalPaid);
        $percentPaid = $totalIssued > 0 ? round(($totalPaid / $totalIssued) * 100, 1) : 0;

        $overdueCount = Invoice::query()
            ->where('status', '!=', InvoiceStatus::Paid->value)
            ->whereDate('due_date', '<', now())
            ->count();

        $topOverdue = Invoice::query()
            ->where('status', '!=', InvoiceStatus::Paid->value)
            ->whereDate('due_date', '<', now())
            ->selectRaw('user_id, SUM(total) as owed, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->orderByDesc('owed')
            ->limit(5)
            ->with('user:id,name,phone')
            ->get();

        $recentPayments = Payment::query()
            ->where('status', 'successful')
            ->latest('paid_at')
            ->limit(8)
            ->with(['user:id,name', 'invoice:id,invoice_number'])
            ->get();

        $totals = [
            'teachers' => User::role('teacher')->count(),
            'active_teachers' => User::role('teacher')->where('is_active', true)->count(),
            'pending_notifications' => NotificationLog::where('status', 'queued')->count(),
            'failed_notifications' => NotificationLog::where('status', 'failed')->count(),
        ];

        return view('livewire.admin.dashboard', [
            'collectedThisMonth' => $collectedThisMonth,
            'totalIssued' => $totalIssued,
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'percentPaid' => $percentPaid,
            'overdueCount' => $overdueCount,
            'topOverdue' => $topOverdue,
            'recentPayments' => $recentPayments,
            'totals' => $totals,
            'monthLabel' => $monthStart->translatedFormat('F Y'),
        ]);
    }
}
