<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductSeeder extends Seeder
{

    private $imgs = [
        'faker/thumbs/desodorante.png',
        'faker/thumbs/exclusivo.png',
        'faker/thumbs/hidratante.png'
    ];

    private $relations = [
        'categories_id' => []
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();
        File::deleteDirectory($dir, true);
        $self = $this;
        Model::reguard();
        Product::factory()->count(100)->make()->each(function (Product $product) use ($self) {
            $self->fetchRelations();
            Product::create(
                array_merge(
                    $product->toArray(),
                    [
                        'thumb_file' => $self->getImageFile(),
                    ],
                    $this->relations
                )
            );
        });
        Model::unguard();
    }

    public function fetchRelations()
    {
        $this->relations['categories_id'] = Category::inRandomOrder()->limit(5)->get()->pluck('id')->toArray();
    }

    public function getImageFile()
    {
        return new UploadedFile(storage_path($this->imgs[array_rand($this->imgs)]), 'img.png');
    }
}
