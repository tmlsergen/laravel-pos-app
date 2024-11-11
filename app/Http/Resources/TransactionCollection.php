<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn ($transaction) => new TransactionResource($transaction))->toArray();
    }
}
