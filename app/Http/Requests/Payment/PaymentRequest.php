<?php

namespace App\Http\Requests\Payment;

use App\Enums\CurrencyEnum;
use App\Enums\PaymentProviderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_holder' => 'required|string',
            'card_number' => ['required', new CardNumber],
            'expiration_year' => ['required', new CardExpirationYear($this->get('expiration_month'))],
            'expiration_month' => ['required', new CardExpirationMonth($this->get('expiration_year'))],
            'cvv' => ['required', new CardCvc($this->get('card_number'))],
            'amount' => 'required|decimal:2',
            'currency' => ['required', new Enum(CurrencyEnum::class)],
            'three_d_secure' => 'required|boolean',
            'provider' => 'required|string|in:'.PaymentProviderEnum::toString(),
        ];
    }
}
