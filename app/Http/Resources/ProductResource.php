<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
            'categories' => CategoryResource::collection($this->categories),
            'brand' => new BrandResource($this->brand),
            'thumb_file_url' => $this->thumb_file_url,
            'price' => number_format($this->price, 2, ',', '')
        ];
    }
}
