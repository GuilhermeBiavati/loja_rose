<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BasicCrudController
{
    private $rules;

    protected $paginationSize = 16;


    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'numeric',
            'amount' => 'integer',
            'is_active' => 'boolean',
            'brand_id' => 'required|string|exists:brands,id,deleted_at,NULL',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'thumb_file' => 'image|max:' . Product::THUMB_FILE_MAX_SIZE,
        ];
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());
        $video = $this->model()::create($validated);
        return new ProductResource($video->refresh());
    }

    public function update(Request $request, $id)
    {
        $video = $this->findOrFail($id);
        $validated = $this->validate($request, $this->rulesUpdate());
        $video->update($validated);
        return new ProductResource($video);
    }

    protected function model()
    {
        return Product::class;
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
        return ProductResource::class;
    }
}
