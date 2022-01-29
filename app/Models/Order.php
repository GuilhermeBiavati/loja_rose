<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $fillable = [
        'amount', 'description', 'price', 'product_id'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => "string",
        'amount' => 'integer',
        'price' => 'float',
    ];

    public $incrementing = false;

    // Evitar realizar regra de negocio nos Controllers
    public static function create(array $attributes = [])
    {
        $product = Product::where('id', $attributes['product_id'])->first();
        $attributes = $attributes + ['price' => $product->price];
        return static::query()->create($attributes);
    }

    public function update(array $attributes = [], array $options = [])
    {
        $product = Product::where('id', $attributes['product_id'])->first();
        $attributes = $attributes + ['price' => $product->price];
        return static::query()->update($attributes, $options);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
