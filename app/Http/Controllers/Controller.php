<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Inventory API",
 *      description="dokumentasi Inventory API",
 *      @OA\Contact(
 *          email="nafidinara@gmail.com"
 *      ),
 * )
 */
/**
 * @OA\Get(
 *     path="/sample",
 *     description="Home page sample",
 *     @OA\Response(response="default", description="Welcome page")
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
