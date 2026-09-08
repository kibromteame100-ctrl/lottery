<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Http\Requests\Admin\UpdateExpenseRequest;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Expense::class);

        $query = Expense::with(['creator', 'approver'])->latest('expense_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")
                                      ->orWhere('description', 'like', "%{$s}%"));
        }
        if ($request->filled('status'))   { $query->where('status', $request->status); }
        if ($request->filled('category')) { $query->where('category', $request->category); }
        if ($request->filled('from'))     { $query->whereDate('expense_date', '>=', $request->from); }
        if ($request->filled('to'))       { $query->whereDate('expense_date', '<=', $request->to); }

        $expenses   = $query->paginate(20)->withQueryString();
        $categories = Expense::categories();

        $stats = [
            'total_this_month'    => Expense::approved()->thisMonth()->sum('amount'),
            'pending_count'       => Expense::pending()->count(),
            'approved_this_month' => Expense::approved()->thisMonth()->count(),
            'total_all_time'      => Expense::approved()->sum('amount'),
        ];

        $byCategory = Expense::approved()
            ->thisMonth()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('admin.expenses.index', compact('expenses', 'stats', 'byCategory', 'categories'));
    }

    public function create()
    {
        Gate::authorize('create', Expense::class);
        return view('admin.expenses.create', ['categories' => Expense::categories()]);
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'private');
        }
        unset($data['receipt']);

        $data['created_by'] = auth()->id();
        $expense = Expense::create($data);

        AuditLog::record('expense_created', Expense::class, $expense->id, [], $expense->toArray());

        return redirect()->route('admin.expenses.index')
            ->with('success', __('expenses.created_successfully'));
    }

    public function show(Expense $expense)
    {
        Gate::authorize('view', $expense);
        $expense->load(['creator', 'approver']);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        Gate::authorize('update', $expense);
        return view('admin.expenses.edit', [
            'expense'    => $expense,
            'categories' => Expense::categories(),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $old  = $expense->toArray();
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('private')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'private');
        }
        unset($data['receipt']);

        $expense->update($data);
        AuditLog::record('expense_updated', Expense::class, $expense->id, $old, $expense->fresh()->toArray());

        return redirect()->route('admin.expenses.index')
            ->with('success', __('expenses.updated_successfully'));
    }

    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);

        if ($expense->receipt_path) {
            Storage::disk('private')->delete($expense->receipt_path);
        }

        AuditLog::record('expense_deleted', Expense::class, $expense->id, $expense->toArray(), []);
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', __('expenses.deleted_successfully'));
    }

    public function approve(Expense $expense)
    {
        Gate::authorize('approve', $expense);

        $expense->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('expense_approved', Expense::class, $expense->id,
            ['status' => 'pending'], ['status' => 'approved', 'approved_by' => auth()->id()]);

        return back()->with('success', __('expenses.approved_successfully'));
    }

    public function reject(Request $request, Expense $expense)
    {
        Gate::authorize('reject', $expense);

        $request->validate(['notes' => ['required', 'string', 'min:5', 'max:500']]);

        $expense->update(['status' => 'rejected', 'notes' => $request->notes]);

        AuditLog::record('expense_rejected', Expense::class, $expense->id,
            ['status' => 'pending'], ['status' => 'rejected', 'notes' => $request->notes]);

        return back()->with('success', __('expenses.rejected_successfully'));
    }

    public function receipt(Expense $expense)
    {
        Gate::authorize('view', $expense);

        if (!$expense->receipt_path || !Storage::disk('private')->exists($expense->receipt_path)) {
            abort(404);
        }

        return response(
            Storage::disk('private')->get($expense->receipt_path),
            200,
            ['Content-Type' => Storage::disk('private')->mimeType($expense->receipt_path)]
        );
    }
}
