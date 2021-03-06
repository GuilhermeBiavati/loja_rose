<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class ProductController extends BasicCrudController
{
    private $rules;

    protected $paginationSize = 0;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'numeric',
            'amount' => 'integer',
            'is_active' => 'boolean',
            'color' => 'string|max:7',
            'brand_id' => 'required|string|exists:brands,id,deleted_at,NULL',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'thumb_file' => 'image|max:' . Product::THUMB_FILE_MAX_SIZE,
        ];
    }

    // public function index(Request $request)
    // {

    //     $data = Product::with('brand', 'categories')->inRandomOrder();

    //     $search = $request->input('search');

    //     if ($search) {
    //         $data = $data->where('name', 'like', "%" . $search . "%");
    //     }

    //     if ($request->input('category')) {
    //         $data = $data->whereHas('categories', function ($q) use ($request) {
    //             return $q->where('id', $request->input('category'));
    //         });
    //     }

    //     if ($request->input('brand')) {
    //         $data = $data->where('brand_id', $request->get('brand'));
    //     }

    //     if (!$this->paginationSize) {
    //         $data = $data->get();
    //     } else {
    //         $data = $data->paginate($this->paginationSize);
    //     }

    //     return ProductResource::collection($data);
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $brand = $request->input('brand');

        $data = Product::with('brand', 'categories')->where('amount', '>', 0)->inRandomOrder();


        if ($search || $category || $brand) {


            if ($search) {
                $data = $data->where('name', 'like', "%" . $search . "%");
            }

            if ($category) {
                $data = $data->whereHas('categories', function ($q) use ($request) {
                    return $q->where('id', $request->input('category'));
                });
            }

            if ($brand) {
                $data = $data->where('brand_id', $request->get('brand'));
            }

            if (!$this->paginationSize) {
                $data = $data->get();
            } else {
                $data = $data->paginate($this->paginationSize);
            }

            return ProductResource::collection($data);
        } else {
            $cache = Cache::remember('products', 5 * 60, function () use ($data) {
                if (!$this->paginationSize) {
                    $data = $data->get();
                } else {
                    $data = $data->paginate($this->paginationSize);
                }

                return $data;
            });

            return ProductResource::collection($cache);
        }
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
