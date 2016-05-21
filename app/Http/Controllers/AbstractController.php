<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AbstractController extends Controller
{
    use Access\AuthorizesRequests,
    	Access\AuthorizesResources,
    	ValidatesRequests;
}
