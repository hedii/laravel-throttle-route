<?php

namespace Hedii\LaravelThrottleRoute\Tests\Fake;

use Illuminate\Http\Response;

class Controller
{
    /**
     * Show the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(): Response
    {
        return new Response();
    }
}
