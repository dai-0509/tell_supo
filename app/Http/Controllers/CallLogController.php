<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallLog::where('user_id', Auth::id())
                        ->whereNotNull('customer_id')
                        ->with(['customer'])
                        ->orderBy('called_at', 'desc');

        // フィルタリング機能
        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('called_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('called_at', '<=', $request->date_to);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_name', 'like', '%' . $request->search . '%');
            });
        }

        $callLogs = $query->paginate(20);
        $customers = Customer::where('user_id', Auth::id())
                           ->select('id', 'company_name', 'contact_name')
                           ->orderBy('company_name')
                           ->get();

        return view('call-logs.index', compact('callLogs', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers = Customer::where('user_id', Auth::id())
                           ->select('id', 'company_name', 'contact_name')
                           ->orderBy('company_name')
                           ->get();
        
        $selectedCustomer = null;
        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::where('user_id', Auth::id())
                                      ->find($request->customer_id);
        }

        return view('call-logs.create', compact('customers', 'selectedCustomer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'called_at' => 'required|date',
            'result' => 'required|in:success,no_answer,busy,appointment,callback,not_interested,invalid_number',
            'next_call_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // 顧客が現在のユーザーのものかチェック
        $customer = Customer::where('user_id', Auth::id())->findOrFail($validated['customer_id']);

        $validated['user_id'] = Auth::id();
        $validated['called_at'] = Carbon::parse($validated['called_at']);

        CallLog::create($validated);

        return redirect()->route('call-logs.index')
                        ->with('success', '架電記録が正常に作成されました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(CallLog $callLog)
    {
        $this->authorize('view', $callLog);
        
        $callLog->load('customer');

        return view('call-logs.show', compact('callLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallLog $callLog)
    {
        $this->authorize('update', $callLog);
        
        $customers = Customer::where('user_id', Auth::id())
                           ->select('id', 'company_name', 'contact_name')
                           ->orderBy('company_name')
                           ->get();
        
        return view('call-logs.edit', compact('callLog', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallLog $callLog)
    {
        $this->authorize('update', $callLog);
        
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'called_at' => 'required|date',
            'result' => 'required|in:success,no_answer,busy,appointment,callback,not_interested,invalid_number',
            'next_call_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // 顧客が現在のユーザーのものかチェック
        $customer = Customer::where('user_id', Auth::id())->findOrFail($validated['customer_id']);

        $validated['called_at'] = Carbon::parse($validated['called_at']);

        $callLog->update($validated);

        return redirect()->route('call-logs.index')
                        ->with('success', '架電記録が正常に更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CallLog $callLog)
    {
        $this->authorize('delete', $callLog);
        
        $callLog->delete();

        return redirect()->route('call-logs.index')
                        ->with('success', '架電記録が正常に削除されました。');
    }

    /**
     * Get call logs for a specific customer (API endpoint).
     */
    public function getCustomerCallLogs(Customer $customer, Request $request)
    {
        $this->authorize('view', $customer);
        
        $query = CallLog::where('user_id', Auth::id())
                       ->where('customer_id', $customer->id)
                       ->whereNotNull('customer_id')
                       ->orderBy('called_at', 'desc');

        if ($request->filled('limit')) {
            $query->limit($request->limit);
        }

        $callLogs = $query->get();
        
        return response()->json($callLogs);
    }

    /**
     * Search call logs for API.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $callLogs = CallLog::where('user_id', Auth::id())
            ->whereNotNull('customer_id')
            ->with('customer:id,company_name,contact_name')
            ->where(function($q) use ($query) {
                $q->whereHas('customer', function($subQ) use ($query) {
                    $subQ->where('company_name', 'like', '%' . $query . '%')
                         ->orWhere('contact_name', 'like', '%' . $query . '%');
                })
                ->orWhere('notes', 'like', '%' . $query . '%');
            })
            ->orderBy('called_at', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json($callLogs);
    }
}