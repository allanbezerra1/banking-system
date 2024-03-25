<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Resources\Admin\IncomeResource;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IncomeApiController extends Controller
{
    public function index(): IncomeResource
    {
        abort_if(Gate::denies('income_access'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncomeResource(Income::with(['incomeCategory', 'user'])->advancedFilter());
    }

    public function store(StoreIncomeRequest $request): JsonResponse
    {
        $income = Income::create($request->validated());

        if ($media = $request->input('document', [])) {
            Media::whereIn('id', data_get($media, '*.id'))
                ->where('model_id', 0)
                ->update(['model_id' => $income->id]);
        }

        return (new IncomeResource($income))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    public function create()
    {
        abort_if(Gate::denies('income_create'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'income_category' => IncomeCategory::get([IncomeCategory::ID, IncomeCategory::NAME]),
                'user'            => User::get([User::ID, User::NAME]),
            ],
        ]);
    }

    public function show(Income $income): IncomeResource
    {
        abort_if(Gate::denies('income_show'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncomeResource($income->load(['incomeCategory', 'user']));
    }

    public function update(UpdateIncomeRequest $request, Income $income): JsonResponse
    {
        $income->update($request->validated());

        $income->updateMedia($request->input('document', []), 'income_document');

        return (new IncomeResource($income))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }

    public function edit(Income $income): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('income_edit'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new IncomeResource($income->load(['incomeCategory', 'user'])),
            'meta' => [
                'income_category' => IncomeCategory::get([IncomeCategory::ID, IncomeCategory::NAME]),
                'user'            => User::get([User::ID, User::NAME]),
            ],
        ]);
    }

    public function destroy(Income $income): Application|Response|ResponseFactory
    {
        abort_if(Gate::denies('income_delete'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        $income->delete();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }

    /**
     * @throws ValidationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function storeMedia(Request $request): JsonResponse
    {
        abort_if(Gate::none(['income_create', 'income_edit']), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model         = new Income();
        $model->id     = $request->input('model_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name'));

        return response()->json($media, ResponseAlias::HTTP_CREATED);
    }

    public function approve(Request $request, Income $income): JsonResponse
    {
        $income->update(['approved' => true]);

        return (new IncomeResource($income))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_ACCEPTED);
    }
}
