<?php

namespace App\Contracts;

use Illuminate\Contracts\Auth\Guard;

interface FormRequestInterface
{
    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    public function authorize(Guard $guard);

    /**
     * Set custom rules for request validator
     *
     * @return array
     */
    public function rules();
}
