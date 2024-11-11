<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PaymentProvider extends Model
{
    protected $table = 'payment_providers';

    protected $guarded = [];

    public function settings(): Collection
    {
        return $this->hasMany(PaymentProviderSetting::class)->get(['key', 'value'])->pluck('value', 'key');
    }
}
