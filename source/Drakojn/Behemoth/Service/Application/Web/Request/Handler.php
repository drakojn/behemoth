<?php
namespace Drakojn\Behemoth\Service\Application\Web\Request;

use Drakojn\Behemoth\Service\Application\Web\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class Handler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function handle(Request $request):Response
    {
        (new EventDispatcher)->dispatch('request', new Event());
        $matcher = $this->buildMatcher($request);
        try {
            $attributes = $matcher->match($request->getPathInfo());
            $commandName = $attributes['command'];
            unset($attributes['command']);
            $command = $this->application->buildCommand($commandName);
            $response = $this->application->buildResponse($command, $attributes, $request);
        } catch (HttpException $exception) {
            $response = new Response('', $exception->getCode());
        } catch (\Throwable $exception) {
            error_log('Problem: ' . $exception->getMessage());
            $response = new Response('', 500);
        }
        return $response;
    }

    protected function buildMatcher(Request $request):Router
    {
        $context = (new RequestContext)->fromRequest($request);
        $closure = [$this->application,'getRouter'];
        return new Router(new ClosureLoader, $closure, [], $context);
    }
}
