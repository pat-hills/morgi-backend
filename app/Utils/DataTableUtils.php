<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class DataTableUtils
{

    public $request;
    public $draw;

    public $offset = 0;
    public $limit = 25;
    public $search = null;

    public $all_rows;
    public $filtered;

    public $sort_key;
    public $sort_direction;


    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->draw = intval($request->draw);

        if($request->has('start')){
            $this->offset = $request->get('start');
        }
        if($request->has('length')){
            $this->limit = $request->get('length');
        }
        if($request->has('search')){
            $this->search = $request->get('search');
        }

        $this->setSorting();
    }

    public static function validate(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'start' => 'string|nullable',
            'length' => 'string|nullable',
            'search' => 'nullable',
            'draw' => 'nullable',
            'status' => 'nullable',
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors()->getMessages(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getResponse($query, $all_rows): array
    {
        $this->all_rows = $all_rows;
        $this->filtered = $all_rows;

        $query->offset($this->offset)
            ->limit($this->limit);

        $data = $query->get();

        return $this->prepareResponse($data);
    }

    private function prepareResponse($data): array
    {
        return [
            'draw'=> intval($this->draw),
            'pages' => ceil($this->all_rows/$this->limit),
            'data' => $data,
            'recordsTotal' => $this->all_rows,
            'recordsFiltered' => $this->filtered
        ];
    }

    private function setSorting(): void
    {
        if (isset($this->request->order[0]['column'])){

            $column_order_id = $this->request->order[0]['column'];

            if(isset($this->request->columns[$column_order_id]['data'])){

                $this->sort_key = strtoupper($this->request->columns[$column_order_id]['data']);
            }

            $this->sort_direction = strtoupper($this->request->order[0]['dir']) ?? 'DESC';
        }
    }
}
