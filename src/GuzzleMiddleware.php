<?php

declare(strict_types = 1);

namespace Arquivei\LaravelPrometheusExporter;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Prometheus\Histogram;

class GuzzleMiddleware
{
    /**
     * @var Histogram
     */
    private $histogram;
    private $extra_values;

    /**
     * @param Histogram $histogram
     */
    public function __construct(Histogram $histogram, $extra_values)
    {
        $this->histogram = $histogram;
        $this->extra_values = $extra_values;
    }

    /**
     * Middleware that calculates the duration of a guzzle request.
     * After calculation it sends metrics to prometheus.
     *
     * @param callable $handler
     *
     * @return callable Returns a function that accepts the next handler.
     */
    public function __invoke(callable $handler) : callable
    {
        return function (Request $request, array $options) use ($handler) {
            $start = microtime(true);
            return $handler($request, $options)->then(
                function (Response $response) use ($request, $start) {
                    $this->histogram->observe(
                        microtime(true) - $start,
                        array_merge(
                            $this->extra_values,
                            [
                                $request->getMethod(),
                                $request->getUri()->getHost(),
                                $response->getStatusCode(),
                            ]
                        )
                    );
                    return $response;
                }
            );
        };
    }
}
