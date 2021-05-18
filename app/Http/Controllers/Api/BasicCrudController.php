<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use ReflectionClass;

abstract class BasicCrudController extends Controller
{

    protected $paginationSize = 15;

    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();
    protected abstract function resource();
    protected abstract function resourceCollection();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = !$this->paginationSize ? $this->model()::all() : $this->model()::paginate($this->paginationSize);
        $resourceCollectionClass = $this->resourceCollection();
        $refClass = new ReflectionClass($this->resourceCollection());
        return $refClass->isSubclassOf(ResourceCollection::class) ? new $resourceCollectionClass($data) : $resourceCollectionClass::collection($data);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        $resource = $this->resource();
        return new $resource($this->model()::create($validatedData)->refresh());
    }

    public function show($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        $resource = $this->resource();
        return new $resource($this->model()::where($keyName, $id)->firstOrFail());
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }
}
