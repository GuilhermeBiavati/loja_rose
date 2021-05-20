<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes, Uuid, UploadFiles;

    const THUMB_FILE_MAX_SIZE = 1024 * 5;

    protected $fillable = [
        'name', 'description', 'is_active', 'price', 'amount', 'thumb_file', 'brand_id', 'color',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => "string",
        'is_active' => 'boolean',
        'price' => 'float',
    ];
    public $incrementing = false;

    public static $fileFields = ['thumb_file'];

    // Evitar realizar regra de negocio nos Controllers
    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);

        try {
            DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            // Upload de arquivos
            $obj->uploadFiles($files);
            DB::commit();
            return $obj;
        } catch (Exception $exception) {
            if (isset($obj)) {
                //Excluir os arquivos de upload
                $obj->deleteFiles($files);
            }
            DB::rollBack();
            throw $exception;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {

        $files = $this->extractFiles($attributes);

        try {
            DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);

            if ($saved) {
                $this->uploadFiles($files);
            }

            DB::commit();

            if ($saved && count($files)) {
                $this->deleteOldFiles();
            }
            return $saved;
        } catch (Exception $exception) {

            //Excluir os arquivos de upload
            $this->deleteFiles($files);

            DB::rollBack();
            throw $exception;
        }
    }

    public static function handleRelations(Product $video, array $attributes)
    {
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    public function uploadDir()
    {
        return $this->id;
    }

    public function getThumbFileUrlAttribute()
    {
        return $this->thumb_file ? $this->getFileUrl($this->thumb_file) : null;
    }
}
