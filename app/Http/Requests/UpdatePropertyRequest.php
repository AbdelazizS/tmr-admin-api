<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;

class UpdatePropertyRequest extends StorePropertyRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        foreach ($rules as $key => $rule) {
            $rules[$key] = 'sometimes|' . $rule;
        }
        return $rules;
    }
}