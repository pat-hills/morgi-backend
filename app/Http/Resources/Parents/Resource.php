<?php

namespace App\Http\Resources\Parents;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * This class is the parent of the other resource class in this repo.
 * Accept obj, array and collection in the construct and push/merge it
 * into $resource prop (collection).
 *
 * @package App\Http\Resources\Parents
 */
class Resource
{
    public $response;
    public $resources;
    public $request;
    protected $pagination;
    protected $requesting_user = null;
    protected $attributes = [];

    public function __construct(Request $request, $resources)
    {
        $this->response = collect([]);
        $this->resources = collect([]);
        $this->request = $request;
        $this->requesting_user = $request->user();

        /*
         * If resources is a collection, merge it into resources collection
         */
        if($resources instanceof Collection){
            $this->resources = $resources;
            return;
        }

        /*
         * If resources is paginated, take data as array
         */
        if($resources instanceof LengthAwarePaginator){
            $this->pagination = collect($resources);
            $resources = $resources->items();
        }

        /*
         * If resources is an obj store it as an obj into resources collection
         */
        if(is_object($resources)){
            $this->resources->push($resources);
            return;
        }

        /*
         * If resources is an empty array, return
         */
        if(is_array($resources) && count($resources)===0){
            return;
        }

        /*
         * If resources is an array and is not set position 0, store it as obj into resources collection
         */
        if(is_array($resources) && !array_key_exists(0, $resources)){
            $this->resources->push((object)$resources);
            return;
        }

        /*
         * If resources is an array, is set position 0 and position 0 is array or object, merge it into resources collection
         */
        if(is_array($resources) && array_key_exists(0, $resources)){

            /*
             * Push resource cast as obj
             */
            foreach ($resources as $resource){
                if(is_array($resource) || is_object($resource)){
                    $this->resources->push((object)$resource);
                }
            }
        }
    }

    protected function addAttributes(array $attributes)
    {
        $this->attributes = array_merge(
            $this->attributes,
            $attributes
        );
    }

    public function small()
    {
        return $this;
    }

    public function regular()
    {
        $this->small();
        return $this;
    }

    public function extended()
    {
        $this->regular();
        return $this;
    }

    public function first(): ?object
    {
        $this->pluckAttributes();
        return ($this->response->count()===0) ? null : $this->response->first();
    }

    public function get(): Collection
    {
        $this->pluckAttributes();

        if(isset($this->pagination)){
            return $this->pagination->merge(['data' => $this->response]);
        }

        return $this->response;
    }

    private function pluckAttributes(): void
    {
        $attributes = $this->attributes;
        $this->response = $this->response->merge($this->resources->map(function ($item) use ($attributes){

            $mapped_item = (object)[];
            foreach ($attributes as $attribute){
                $mapped_item->$attribute = $item->$attribute ?? null;
            }

            return $mapped_item;
        }));
    }

    public static function compute(Request $request, $resources, string $response_type = null): Resource
    {
        $class = static::class;
        $class_instance = new $class($request, $resources);

        /*
         * By default take $response_type by function's call, if is not set and is set in the request,
         * use the request's one
         */
        if(!isset($response_type) && isset($request->response_type) && in_array($request->response_type, ['small', 'regular', 'extended'])){
            $response_type = $request->response_type;
        }

        switch ($response_type){
            case 'small':
                $class_instance->small();
                break;
            case 'extended':
                $class_instance->extended();
                break;
            default:
                $class_instance->regular();
                break;
        }

        return $class_instance;
    }
}
