<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends BasicCrudController
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

        $data = Cache::remember('categories', 5 * 60, function () use ($paginationSize) {
            $data = Category::orderBy("name");
            if (!$paginationSize) {
                $data = $data->get();
            } else {
                $data = $data->paginate($paginationSize);
            }
            return $data;
        });

        return CategoryResource::collection($data);
    }

    protected function model()
    {
        return Category::class;
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
        return CategoryResource::class;
    }
}
