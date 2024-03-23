<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Http\Resources\Admin\IncomeResource;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IncomeApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('income_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncomeResource(Income::with(['incomeCategory', 'user'])->advancedFilter());
    }

    public function store(StoreIncomeRequest $request)
    {
        $income = Income::create($request->validated());

        if ($media = $request->input('document', [])) {
            Media::whereIn('id', data_get($media, '*.id'))
                ->where('model_id', 0)
                ->update(['model_id' => $income->id]);
        }

        return (new IncomeResource($income))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function create()
    {
        abort_if(Gate::denies('income_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'meta' => [
                'income_category' => IncomeCategory::get(['id', 'name']),
                'user'            => User::get(['id', 'name']),
            ],
        ]);
    }

    public function show(Income $income)
    {
        abort_if(Gate::denies('income_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncomeResource($income->load(['incomeCategory', 'user']));
    }

    public function update(UpdateIncomeRequest $request, Income $income)
    {
        $income->update($request->validated());

        $income->updateMedia($request->input('document', []), 'income_document');

        return (new IncomeResource($income))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function edit(Income $income)
    {
        abort_if(Gate::denies('income_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new IncomeResource($income->load(['incomeCategory', 'user'])),
            'meta' => [
                'income_category' => IncomeCategory::get(['id', 'name']),
                'user'            => User::get(['id', 'name']),
            ],
        ]);
    }

    public function destroy(Income $income)
    {
        abort_if(Gate::denies('income_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $income->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeMedia(Request $request)
    {
        abort_if(Gate::none(['income_create', 'income_edit']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model         = new Income();
        $model->id     = $request->input('model_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name'));

        return response()->json($media, Response::HTTP_CREATED);
    }
}
