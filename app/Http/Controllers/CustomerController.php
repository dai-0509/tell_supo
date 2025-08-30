<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::where('user_id', Auth::id())
                        ->with('callLogs')
                        ->orderBy('created_at', 'desc');

        // フィルタリング機能
        if ($request->filled('temperature_rating')) {
            $query->where('temperature_rating', $request->temperature_rating);
        }

        if ($request->filled('area')) {
            $query->where('area', 'like', '%' . $request->area . '%');
        }

        if ($request->filled('industry')) {
            $query->where('industry', 'like', '%' . $request->industry . '%');
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_name', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->paginate(15);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'industry' => 'nullable|string|max:100',
            'temperature_rating' => 'nullable|in:A,B,C,D,E,F',
            'area' => 'nullable|string|max:50',
            'status' => 'required|in:new,contacted,interested,not_interested,callback_scheduled,closed',
            'priority' => 'required|in:low,medium,high',
            'memo' => 'nullable|string'
        ]);

        $validated['user_id'] = Auth::id();

        Customer::create($validated);

        return redirect()->route('customers.index')
                        ->with('success', '顧客が正常に作成されました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        
        $customer->load(['callLogs' => function($query) {
            $query->orderBy('called_at', 'desc')->limit(10);
        }]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone' => 'nullable|string|max:20',
            'industry' => 'nullable|string|max:100',
            'temperature_rating' => 'nullable|in:A,B,C,D,E,F',
            'area' => 'nullable|string|max:50',
            'status' => 'required|in:new,contacted,interested,not_interested,callback_scheduled,closed',
            'priority' => 'required|in:low,medium,high',
            'memo' => 'nullable|string'
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
                        ->with('success', '顧客情報が正常に更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        
        $customer->delete();

        return redirect()->route('customers.index')
                        ->with('success', '顧客が正常に削除されました。');
    }
}
