<?php

namespace App\Http\Controllers;

use App\Utils\MorgiPaginator;
use Illuminate\Container\Container;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function customPaginate($results, $pageSize = 15, $options = []){

        $results = ($results instanceof Collection) ? $results : Collection::make($results);

        $page = Paginator::resolveCurrentPage();

        $total = $results->count();

        $items = self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $items = collect($items);
        $data = $items['data'];
        $items->put('data', $data);

        //TODO find a way to do this in a more generic way
        $items->put('metadata', $options);

        return $items;
    }

    protected static function paginator($items, $total, $perPage, $currentPage, $options){

        return Container::getInstance()->makeWith(MorgiPaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }
}
