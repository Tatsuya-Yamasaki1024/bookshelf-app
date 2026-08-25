<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    /**
     * ログインユーザーの読書計画一覧を表示する。
     *
     * statusが指定されている場合は、ステータスで絞り込む。
     *
     * @param  Request  $request  読書計画一覧のリクエスト
     * @return View 読書計画一覧画面
     */
    public function index(Request $request): View
    {
        $query = ReadingPlan::with('book')
            ->where('user_id', auth()->id());

        $currentStatus = $request->status;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $readingPlans = $query
            ->orderByRaw('
            CASE status
                WHEN ? THEN 1
                WHEN ? THEN 2
                WHEN ? THEN 3
                ELSE 4
            END
        ', [
                ReadingPlanStatus::Expired->value,
                ReadingPlanStatus::InProgress->value,
                ReadingPlanStatus::Completed->value,
            ])
            ->orderBy('target_date')
            ->get();

        return view('reading-plans.index', compact(
            'readingPlans',
            'currentStatus'
        ));
    }

    /**
     * 読書計画を読了状態に更新する。
     *
     * @param  ReadingPlan  $plan  読了状態にする読書計画
     * @return RedirectResponse 読書計画一覧画面へのリダイレクト
     */
    public function complete(ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('update', $plan);

        $plan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を読了にしました。');
    }

    /**
     * 読書計画の作成画面を表示する。
     *
     * @return View 読書計画作成画面
     */
    public function create(): View
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 新しい読書計画を作成する。
     *
     * @param  StoreReadingPlanRequest  $request  読書計画作成リクエスト
     * @return RedirectResponse 読書計画一覧画面へのリダイレクト
     */
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id();
        $validated['status'] = ReadingPlanStatus::InProgress;

        ReadingPlan::create($validated);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を作成しました。');
    }

    /**
     * 読書計画の編集画面を表示する。
     *
     * @param  ReadingPlan  $plan  編集対象の読書計画
     * @return View 読書計画編集画面
     */
    public function edit(ReadingPlan $plan): View
    {
        $this->authorize('update', $plan);

        return view('reading-plans.edit', [
            'readingPlan' => $plan,
        ]);
    }

    /**
     * 読書計画を更新する。
     *
     * @param  UpdateReadingPlanRequest  $request  読書計画更新リクエスト
     * @param  ReadingPlan  $plan  更新対象の読書計画
     * @return RedirectResponse 読書計画一覧画面へのリダイレクト
     */
    public function update(
        UpdateReadingPlanRequest $request,
        ReadingPlan $plan
    ): RedirectResponse {
        $this->authorize('update', $plan);

        $plan->update([
            'target_date' => $request->validated('target_date'),
            'status' => ReadingPlanStatus::InProgress,
            'completed_at' => null,
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画を削除する。
     *
     * @param  ReadingPlan  $plan  削除対象の読書計画
     * @return RedirectResponse 読書計画一覧画面へのリダイレクト
     */
    public function destroy(ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $plan->delete();

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }
}
