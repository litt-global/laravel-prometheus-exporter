<?php

namespace Arquivei\LaravelPrometheusExporter;

use Closure;
use Illuminate\Support\Facades\Route as RouteFacade;
use Prometheus\Histogram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusLaravelRouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next) : Response
    {
        $extra_labels = config('prometheus.extra_labels');
        $extra_labels_keys = array_keys($extra_labels);
        $extra_labels_vals = array_values($extra_labels);

        $matchedRoute = $this->getMatchedRoute($request);

        $start = microtime(true);
        /** @var Response $response */
        $response = $next($request);
        $duration = microtime(true) - $start;
        /** @var PrometheusExporter $exporter */
        $exporter = app('prometheus');
        $histogram = $exporter->getOrRegisterHistogram(
            'response_time_seconds',
            'It observes response time.',
            array_merge(
                $extra_labels_keys,
                [
                    'method',
                    'route',
                    'status_code',
                ]
            )
        );
        /** @var  Histogram $histogram */
        $histogram->observe(
            $duration,
            array_merge(
                $extra_labels_vals,
                [
                    $request->method(),
                    $matchedRoute->uri(),
                    $response->getStatusCode(),
                ]
            )
        );
        return $response;
    }

    public function getMatchedRoute(Request $request)
    {
        $routeCollection = RouteFacade::getRoutes();
        return $routeCollection->match($request);
    }
}
