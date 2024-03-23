<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ChartsService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    /**
     * @throws Exception
     */
    public function index(): JsonResponse
    {
        $latest0 = new ChartsService([
            'title'            => 'Expenses',
            'chart_type'       => 'latest',
            'model'            => 'App\Models\Expense',
            'column_class'     => 'col-md-6',
            'fields'           => ['entry_date', 'amount', 'description'],
            'limit'            => 10,
            'filter_by_field'  => 'created_at',
            'filter_by_period' => 7,
        ]);

        $latest1 = new ChartsService([
            'title'            => 'Income',
            'chart_type'       => 'latest',
            'model'            => 'App\Models\Income',
            'column_class'     => 'col-md-6',
            'fields'           => ['entry_date', 'amount', 'description', 'approved'],
            'limit'            => 10,
            'filter_by_field'  => 'created_at',
            'filter_by_period' => 7,
        ]);

        return response()->json(compact('latest0', 'latest1'));
    }
}
