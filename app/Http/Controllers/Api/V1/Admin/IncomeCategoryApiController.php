<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncomeCategoryRequest;
use App\Http\Requests\UpdateIncomeCategoryRequest;
use App\Http\Resources\Admin\IncomeCategoryResource;
use App\Models\IncomeCategory;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IncomeCategoryApiController extends Controller
{
    public function index(): IncomeCategoryResource
    {
        abort_if(Gate::denies('income_category_access'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncomeCategoryResource(IncomeCategory::advancedFilter());
    }

    public function store(StoreIncomeCategoryRequest $request): JsonResponse
    {
        $incomeCategory = IncomeCategory::create($request->validated());

        return (new IncomeCategoryResource($incomeCategory))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function create(): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('income_category_create'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [],
        ]);
    }

    public function show(IncomeCategory $incomeCategory): IncomeCategoryResource
    {
        abort_if(Gate::denies('income_category_show'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncomeCategoryResource($incomeCategory);
    }

    public function update(UpdateIncomeCategoryRequest $request, IncomeCategory $incomeCategory): JsonResponse
    {
        $incomeCategory->update($request->validated());

        return (new IncomeCategoryResource($incomeCategory))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function edit(IncomeCategory $incomeCategory): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('income_category_edit'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new IncomeCategoryResource($incomeCategory),
            'meta' => [],
        ]);
    }

    public function destroy(IncomeCategory $incomeCategory): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('income_category_delete'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        $incomeCategory->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
