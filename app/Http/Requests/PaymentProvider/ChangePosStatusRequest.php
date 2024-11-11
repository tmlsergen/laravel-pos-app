<?php

namespace App\Http\Requests\PaymentProvider;

use App\Enums\PaymentProviderEnum;
use Illuminate\Foundation\Http\FormRequest;

class ChangePosStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'slug' => 'required|string|in:'.PaymentProviderEnum::toString(),
            'status' => 'required|boolean',
        ];
    }
}
