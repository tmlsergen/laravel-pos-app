<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentProviderCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn ($provider) => new PaymentProviderResource($provider))->toArray();
    }
}
