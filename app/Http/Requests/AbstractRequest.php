<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Auth\Guard;
use App\Contracts\FormRequestInterface;
use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractRequest extends FormRequest implements FormRequestInterface
{
    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    public function authorize(Guard $guard)
    {
        return $guard->check();
    }

    /**
     * Set custom rules for request validator
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
