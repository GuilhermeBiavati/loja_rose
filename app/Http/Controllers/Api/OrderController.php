<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BasicCrudController
{
    private $rules;

    protected $paginationSize = 0;

    public function __construct()
    {
        $this->rules = [
            'description' => 'nullable',
            'amount' => 'integer',
            'product_id' => 'required|string|exists:products,id,deleted_at,NULL',
        ];
    }

    // public function index(Request $request)
    // {
    //     $search = $request->input('search');
    //     $category = $request->input('category');
    //     $brand = $request->input('brand');

    //     if ($search || $category || $brand) {
    //         $data = Order::with('brand', 'categories')->inRandomOrder();

    //         if ($search) {
    //             $data = $data->where('name', 'like', "%" . $search . "%");
    //         }

    //         if ($category) {
    //             $data = $data->whereHas('categories', function ($q) use ($request) {
    //                 return $q->where('id', $request->input('category'));
    //             });
    //         }

    //         if ($brand) {
    //             $data = $data->where('brand_id', $request->get('brand'));
    //         }

    //         if (!$this->paginationSize) {
    //             $data = $data->get();
    //         } else {
    //             $data = $data->paginate($this->paginationSize);
    //         }
    //     } else {
    //         $data = Cache::remember('Orders', 5 * 60, function () {
    //             $data = Order::with('brand', 'categories')->inRandomOrder();
    //             if (!$this->paginationSize) {
    //                 $data = $data->get();
    //             } else {
    //                 $data = $data->paginate($this->paginationSize);
    //             }

    //             return $data;
    //         });
    //     }

    //     return OrderResource::collection($data);
    // }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());
        $order = $this->model()::create($validated);
        return new OrderResource($order->refresh());
    }

    public function update(Request $request, $id)
    {
        $order = $this->findOrFail($id);
        $validated = $this->validate($request, $this->rulesUpdate());
        $order->update($validated);
        return new OrderResource($order);
    }

    protected function model()
    {
        return Order::class;
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
        return OrderResource::class;
    }
}
