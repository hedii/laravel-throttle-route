[![Build Status](https://travis-ci.org/hedii/laravel-throttle-route.svg?branch=master)](https://travis-ci.org/hedii/laravel-throttle-route)

# Laravel Throttle Route

A Laravel package to throttle requests based on route name.

The default Laravel request throttler acts as a global throttler based on user ID or IP. This package allows to have a request limit set by route, by using the route name to resolve the request signature.

## Installation

Install with [composer](https://getcomposer.org/doc/00-intro.md)

```
composer require hedii/laravel-throttle-route
```

## Usage

Add the middleware in your route, and use it as the default Laravel ThrottleRequests middleware.

Don't forget to set a route name.

```php
Route::get('/first', 'FirstController@show')
    ->middleware(\Hedii\LaravelThrottleRoute\ThrottleRequests::class . ':20,1')
    ->name('first');
    
Route::get('/second', 'SecondController@show')
    ->middleware(\Hedii\LaravelThrottleRoute\ThrottleRequests::class . ':60,1')
    ->name('second');
```

## Testing

```
composer test
```

## License
laravel-throttle-route is released under the MIT Licence. See the bundled [LICENSE](https://github.com/hedii/laravel-throttle-route/blob/master/LICENSE.md) file for details.
