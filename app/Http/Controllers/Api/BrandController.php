<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BrandController extends BasicCrudController
{

    protected $paginationSize = 0;

    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean'
    ];

    public function index(Request $request)
    {

        $paginationSize = $this->paginationSize;

        $data = Cache::remember('brands', 5 * 60, function () use ($paginationSize) {
            $data = Brand::orderBy("name");
            if (!$paginationSize) {
                $data = $data->get();
            } else {
                $data = $data->paginate($paginationSize);
            }
            return $data;
        });

        return BrandResource::collection($data);
    }

    protected function model()
    {
        return Brand::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }
    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return BrandResource::class;
    }
}
