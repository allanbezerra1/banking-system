<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Http\Resources\Admin\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseCategoryApiController extends Controller
{
    public function index(): ExpenseCategoryResource
    {
        abort_if(Gate::denies('expense_category_access'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExpenseCategoryResource(ExpenseCategory::advancedFilter());
    }

    public function store(StoreExpenseCategoryRequest $request): JsonResponse
    {
        $expenseCategory = ExpenseCategory::create($request->validated());

        return (new ExpenseCategoryResource($expenseCategory))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function create(): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('expense_category_create'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [],
        ]);
    }

    public function show(ExpenseCategory $expenseCategory): ExpenseCategoryResource
    {
        abort_if(Gate::denies('expense_category_show'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new ExpenseCategoryResource($expenseCategory);
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): JsonResponse
    {
        $expenseCategory->update($request->validated());

        return (new ExpenseCategoryResource($expenseCategory))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function edit(ExpenseCategory $expenseCategory): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('expense_category_edit'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new ExpenseCategoryResource($expenseCategory),
            'meta' => [],
        ]);
    }

    public function destroy(ExpenseCategory $expenseCategory): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('expense_category_delete'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        $expenseCategory->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
