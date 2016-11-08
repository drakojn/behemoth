<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Service\Application\Web;

use Drakojn\Behemoth\Service\Application\ApplicationInterface;
use Drakojn\Behemoth\Service\Application\Web\Request\Handler;
use Drakojn\Behemoth\Service\Bootstrap;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class Application implements HttpKernelInterface, ApplicationInterface
{
    /** @var RouteCollection */
    protected $routes;
    /** @var Bootstrap */
    protected $bootstrap;

    public function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    public function getContainer() : Container
    {
        return $this->bootstrap->getContainer();
    }

    public function getBootstrap() : Bootstrap
    {
        return $this->bootstrap;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true):Response
    {
        if (!($type == self::MASTER_REQUEST && $catch == false)) {
            throw new \RuntimeException('Wrong Call');
        }
        $handler = new Handler($this);
        $response = $handler->handle($request);
        return $response;
    }

    public function buildCommand(string $class):Command
    {
        return new $class($this->bootstrap->getContainer());
    }

    public function buildResponse(Command $command, Request $request, array $attributes = []):Response
    {
        return $command->execute($request, $attributes);
    }

    public function buildRouter():RouteCollection
    {
        $cache = $this->bootstrap->getCache();
        $cachedRouter = $cache->getItem('router.routes');
        $router = $cachedRouter->get();
        $container = $this->getBootstrap()->getContainer();
        if (!$cachedRouter->isHit()) {
            $locator = $container->get('file.locator');
            $routesLoader = new YamlFileLoader($locator);
            $router = new RouteCollection();
            $routePath = $this->bootstrap->getApplicationPath() . '/definition/routes/index.yaml';
            $router->addCollection($routesLoader->load($routePath));
            $cachedRouter->set($router);
            $cache->save($cachedRouter);
        }
        return $router;
    }

    public function __invoke():string
    {
        $request = Request::createFromGlobals();
        $response = $this->handle($request);
        $response->send();
        return '';
    }
}
