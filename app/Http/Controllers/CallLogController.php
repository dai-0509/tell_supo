<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallLog\StoreCallLogRequest;
use App\Http\Requests\CallLog\UpdateCallLogRequest;
use App\Models\CallLog;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CallLogController extends Controller
{
    use AuthorizesRequests;

    /**
     * 架電記録一覧画面を表示する
     *
     * @return View 架電記録一覧ページ
     */
    public function index(): View
    {
        $callLogs = CallLog::forUser(auth()->id())
            ->with(['customer'])
            ->latest('started_at')
            ->paginate(20);

        return view('pages.call-logs.index', compact('callLogs'));
    }

    /**
     * 架電記録登録フォーム画面を表示する
     *
     * @return View 架電記録登録ページ
     */
    public function create(): View
    {
        $customers = Customer::forUser(auth()->id())
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'contact_name']);

        $resultOptions = CallLog::getResultOptions();

        return view('pages.call-logs.create', compact('customers', 'resultOptions'));
    }

    /**
     * 新規架電記録データを保存する
     *
     * @param  StoreCallLogRequest  $request  バリデーション済みの架電記録情報
     * @return RedirectResponse 架電記録詳細画面へのリダイレクト
     */
    public function store(StoreCallLogRequest $request): RedirectResponse
    {
        $callLog = CallLog::create($request->validated());

        return redirect()
            ->route('call-logs.show', $callLog)
            ->with('success', '架電記録を登録しました');
    }

    /**
     * 指定された架電記録の詳細画面を表示する
     *
     * @param  CallLog  $callLog  表示対象の架電記録モデル
     * @return View 架電記録詳細ページ
     */
    public function show(CallLog $callLog): View
    {
        $this->authorize('view', $callLog);

        $callLog->load('customer');

        return view('pages.call-logs.show', compact('callLog'));
    }

    /**
     * 指定された架電記録の編集フォーム画面を表示する
     *
     * @param  CallLog  $callLog  編集対象の架電記録モデル
     * @return View 架電記録編集ページ
     */
    public function edit(CallLog $callLog): View
    {
        $this->authorize('update', $callLog);

        $customers = Customer::forUser(auth()->id())
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'contact_name']);

        $resultOptions = CallLog::getResultOptions();

        return view('pages.call-logs.edit', compact('callLog', 'customers', 'resultOptions'));
    }

    /**
     * 指定された架電記録データを更新する
     *
     * @param  UpdateCallLogRequest  $request  バリデーション済みの更新情報
     * @param  CallLog  $callLog  更新対象の架電記録モデル
     * @return RedirectResponse 架電記録詳細画面へのリダイレクト
     */
    public function update(UpdateCallLogRequest $request, CallLog $callLog): RedirectResponse
    {
        $callLog->update($request->validated());

        return redirect()
            ->route('call-logs.show', $callLog)
            ->with('success', '架電記録を更新しました');
    }

    /**
     * 指定された架電記録データを削除する
     *
     * @param  CallLog  $callLog  削除対象の架電記録モデル
     * @return RedirectResponse 架電記録一覧画面へのリダイレクト
     */
    public function destroy(CallLog $callLog): RedirectResponse
    {
        $this->authorize('delete', $callLog);

        $callLog->delete();

        return redirect()
            ->route('call-logs.index')
            ->with('success', '架電記録を削除しました');
    }
}
