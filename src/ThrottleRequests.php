<?php

namespace Hedii\LaravelThrottleRoute;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Middleware\ThrottleRequests as Middleware;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ThrottleRequests extends Middleware
{
    /**
     * The logger implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * The request ip address.
     *
     * @var string
     */
    protected $ip;

    /**
     * The request url.
     *
     * @var string
     */
    protected $url;

    /**
     * The request user agent.
     *
     * @var string
     */
    protected $userAgent;

    /**
     * ThrottleRequests constructor.
     *
     * @param \Illuminate\Cache\RateLimiter $limiter
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(RateLimiter $limiter, LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct($limiter);
    }

    /**
     * Resolve request signature.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     * @throws \RuntimeException
     */
    protected function resolveRequestSignature($request): string
    {
        $this->ip = $request->ip();
        $this->url = $request->url();
        $this->userAgent = $request->userAgent() ?: 'unknown';

        if ($route = $request->route()) {
            if ($user = $request->user()) {
                return sha1("{$user->getAuthIdentifier()}|{$route->getName()}");
            }

            return sha1("{$route->getDomain()}|{$route->getName()}|{$this->ip}");
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param string $key
     * @param int $maxAttempts
     * @return \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    protected function buildException($key, $maxAttempts): ThrottleRequestsException
    {
        $this->logger->info('Request throttled', [
            'ip' => $this->ip,
            'url' => $this->url,
            'user_agent' => $this->userAgent
        ]);

        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return new ThrottleRequestsException(
            'Too Many Attempts.', null, $headers
        );
    }
}
