<?php

namespace App\Http\Requests;

use App\Models\Income;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreIncomeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('income_create');
    }

    public function rules()
    {
        return [
            'income_category_id' => [
                'integer',
                'exists:income_categories,id',
                'nullable',
            ],
            'user_id' => [
                'integer',
                'exists:users,id',
                'nullable',
            ],
            'entry_date' => [
                'date_format:' . config('project.date_format'),
                'required',
            ],
            'amount' => [
                'numeric',
                'required',
            ],
            'description' => [
                'string',
                'nullable',
            ],
            'approved' => [
                'boolean',
            ],
            'document' => [
                'array',
                'required',
            ],
            'document.*.id' => [
                'integer',
                'exists:media,id',
            ],
        ];
    }
}
