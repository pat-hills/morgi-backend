<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;


class LeaderCcbillDataResource extends Resource
{
    public function small(): LeaderCcbillDataResource
    {
        $this->addAttributes([
            'id',
            'active',
            'last4',
            'expDate',
        ]);

        return $this;
    }

    public function extended(): LeaderCcbillDataResource
    {
        $this->small();

        return $this;
    }
}
