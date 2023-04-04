<?php

namespace Dbaeka\StripePayment\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Dbaeka\StripePayment\Jobs\HandleStripeCallback;
use Illuminate\Routing\Controller as BaseController;

class CallbackController extends BaseController
{
    public function handle(Request $request): Response
    {
        $payload = $request->all();
        dispatch(new HandleStripeCallback($payload));
        return response()->json()->setStatusCode(200);
    }
}
