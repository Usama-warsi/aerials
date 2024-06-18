<?php

namespace Botble\Braintree\Http\Requests;

use Botble\Support\Http\Requests\Request;

class BraintreePaymentCallbackRequest extends Request
{
    public function rules(): array
    {
        return [
            'session_id' => 'required|size:66',
        ];
    }
}
