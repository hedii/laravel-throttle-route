<?php

namespace Hedii\LaravelThrottleRoute\Tests;

use Hedii\LaravelThrottleRoute\Tests\Fake\Controller;
use Hedii\LaravelThrottleRoute\ThrottleRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class ThrottleRequestsTest extends TestCase
{
    /** @test */
    public function it_should_throttle_requests_based_on_route_name(): void
    {
        Route::get('/first', Controller::class . '@show')
            ->middleware(ThrottleRequests::class . ':3,1')
            ->name('first');

        Route::get('/second', Controller::class . '@show')
            ->middleware(ThrottleRequests::class . ':5,1')
            ->name('second');

        Route::get('/third', Controller::class . '@show')
            ->middleware(ThrottleRequests::class . ':3,1')
            ->name('third');

        $this->get('/first')->assertOk();
        $this->get('/first')->assertOk();
        $this->get('/first')->assertOk();
        $this->get('/second')->assertOk();
        $this->get('/first')->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
        $this->get('/first')->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
        $this->get('/third')->assertOk();
        $this->get('/second')->assertOk();
        $this->get('/second')->assertOk();
        $this->get('/second')->assertOk();
        $this->get('/second')->assertOk();
        $this->get('/second')->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
        $this->get('/second')->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
        $this->get('/second')->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
        $this->get('/third')->assertOk();
        $this->get('/third')->assertOk();
        $this->get('/third')->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
