<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends AbstractAdminController
{
    /**
     * Form Request handler class.
     *
     * @var string
     */
    protected $formRequestClass = UserRequest::class;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
