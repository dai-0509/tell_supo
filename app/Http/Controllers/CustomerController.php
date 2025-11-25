<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * 顧客管理コントローラー
 *
 * 顧客データのCRUD操作を提供するRESTfulコントローラー
 * ユーザー認可（Policy）、データバリデーション（FormRequest）を統合
 */
class CustomerController extends Controller
{
    /**
     * AuthorizesRequestsトレイト
     *
     * authorize()メソッドを提供し、Policyベースの認可を実行
     * $this->authorize('view', $customer) でCustomerPolicyのviewメソッドを呼び出し
     */
    use AuthorizesRequests;

    /**
     * 顧客一覧画面を表示する
     *
     * @return View 顧客一覧ページ
     */
    public function index(): View
    {
        $customers = Customer::forUser(auth()->id())
            ->latest()
            ->paginate(20);

        return view('pages.customers.index', compact('customers'));
    }

    /**
     * 顧客登録フォーム画面を表示する
     *
     * @return View 顧客登録ページ
     */
    public function create(): View
    {
        return view('pages.customers.create');
    }

    /**
     * 新規顧客データを保存する
     *
     * @param  StoreCustomerRequest  $request  バリデーション済みの顧客情報
     * @return RedirectResponse|JsonResponse 顧客詳細画面へのリダイレクト又はJSON応答
     */
    public function store(StoreCustomerRequest $request): RedirectResponse|JsonResponse
    {
        $customer = Customer::create($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '顧客を登録しました',
                'customer' => $customer
            ]);
        }

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', '顧客を登録しました');
    }

    /**
     * 指定された顧客の詳細画面を表示する
     *
     * @param  Customer  $customer  表示対象の顧客モデル
     * @return View 顧客詳細ページ
     */
    public function show(Customer $customer): View
    {
        // authorize()はAuthorizesRequestsトレイトから使用可能
        $this->authorize('view', $customer);

        return view('pages.customers.show', compact('customer'));
    }

    /**
     * 指定された顧客の編集フォーム画面を表示する
     *
     * @param  Customer  $customer  編集対象の顧客モデル
     * @return View 顧客編集ページ
     */
    public function edit(Customer $customer): View
    {
        // authorize()はAuthorizesRequestsトレイトから使用可能
        $this->authorize('update', $customer);

        return view('pages.customers.edit', compact('customer'));
    }

    /**
     * 指定された顧客データを更新する
     *
     * @param  UpdateCustomerRequest  $request  バリデーション済みの更新情報
     * @param  Customer  $customer  更新対象の顧客モデル
     * @return RedirectResponse 顧客詳細画面へのリダイレクト
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', '顧客情報を更新しました');
    }

    /**
     * 指定された顧客データを削除する
     *
     * @param  Customer  $customer  削除対象の顧客モデル
     * @return RedirectResponse 顧客一覧画面へのリダイレクト
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        // authorize()はAuthorizesRequestsトレイトから使用可能
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', '顧客を削除しました');
    }

    /**
     * React components用の顧客一覧APIエンドポイント
     *
     * @return JsonResponse 顧客データのJSON応答
     */
    public function apiIndex(): JsonResponse
    {
        $customers = Customer::forUser(auth()->id())
            ->select('id', 'company_name', 'contact_name')
            ->orderBy('company_name')
            ->get();

        return response()->json($customers);
    }
}
