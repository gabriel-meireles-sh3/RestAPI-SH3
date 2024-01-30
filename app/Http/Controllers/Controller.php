<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="SH3 API Services",
 *     version="1.0",
 *     description="API developed for SH3 backend",
 *     @OA\Contact(
 *         email="gabriel.meireles@sh3.com.br",
 *         name="Gabriel Meireles"
 *     ),
 * ),
 * 
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
