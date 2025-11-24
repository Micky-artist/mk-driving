<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\ControllerMiddlewareOptions;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Register middleware on the controller.
     *
     * @param  string  $middleware
     * @param  array  $options
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        return new ControllerMiddlewareOptions($options);
    }
}
