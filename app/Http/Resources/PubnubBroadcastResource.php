<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class PubnubBroadcastResource extends Resource
{
    public function small(): PubnubBroadcastResource
    {
        $this->attributes = [
            'type'
        ];

        $this->addTransactionToResources();

        return $this;
    }

    public function regular(): PubnubBroadcastResource
    {
        $this->small();
        return $this;
    }

    public function extended(): PubnubBroadcastResource
    {
        $this->regular();
        return $this;
    }

    public function first(): ?object
    {
        $item = parent::first();
        return $item ?? null;
    }

    public function get(): Collection
    {
        $response = parent::get();
        if(isset($this->pagination)){
            $data = $response->get('data');
            return $response->merge(['data' => $data]);
        }

        return $response;
    }

    private function addTransactionToResources(): void
    {
        $transactions = Transaction::query()->findMany(
            $this->resources->pluck('transaction_id')->toArray()
        );
        $transactions_resources = TransactionResource::compute($this->request, $transactions, 'small')->get();

        foreach ($this->resources as $resource) {
            $resource->transaction = $transactions_resources->where('id', $resource->transaction_id)->first();
        }

        $this->attributes[] = 'transaction';
    }
}
