<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\Admin\ExpenseResource;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseApiController extends Controller
{
    public function index(): ExpenseResource
    {
        abort_if(Gate::denies('expense_access'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExpenseResource(Expense::with(['expenseCategory', 'user'])->advancedFilter());
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create($request->validated());

        return (new ExpenseResource($expense))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function create(): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('expense_create'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'expense_category' => ExpenseCategory::get(['id', 'name']),
                'user'             => User::get(['id', 'name']),
            ],
        ]);
    }

    public function show(Expense $expense): ExpenseResource
    {
        abort_if(Gate::denies('expense_show'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExpenseResource($expense->load(['expenseCategory', 'user']));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $expense->update($request->validated());

        return (new ExpenseResource($expense))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function edit(Expense $expense): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('expense_edit'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new ExpenseResource($expense->load(['expenseCategory', 'user'])),
            'meta' => [
                'expense_category' => ExpenseCategory::get(['id', 'name']),
                'user'             => User::get(['id', 'name']),
            ],
        ]);
    }

    public function destroy(Expense $expense): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('expense_delete'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
