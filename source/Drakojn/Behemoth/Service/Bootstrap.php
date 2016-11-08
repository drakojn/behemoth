<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Service;

use Cekurte\Environment\Environment;
use Drakojn\Behemoth\Helper\Environment\Filter;
use Drakojn\Behemoth\Service\Application\ApplicationInterface;
use Drakojn\Behemoth\Service\Application\Web\Application as Web;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Bootstrap
{
    protected $container;
    protected $applicationPath;
    protected $freshContainer = false;
    protected $cache;
    protected $preffix;

    public function __construct(string $applicationPath, CacheItemPoolInterface $cache, string $preffix = 'APP')
    {
        $this->applicationPath = $applicationPath;
        $this->cache = $cache;
        $this->container = $this->cache->getItem('definition.container');
        if (!$this->container) {
            $this->container = $this->buildContainer();
        }
        $this->preffix = $preffix;
    }

    public function getApplicationPath(): string
    {
        return $this->applicationPath;
    }

    public function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    public function getContainer():Container
    {
        return $this->container;
    }

    protected function buildContainer(): Container
    {
        $container = new ContainerBuilder();
        $container->set('bootstrap', $this);
        $container->setParameter('app.path', __DIR__);
        foreach ($this->loadEnvironmentVariables() as $variable => $value) {
            $container->setParameter($variable, $value);
        }
        $loader = new YamlFileLoader($container, new FileLocator($this->applicationPath . '/definition/'));
        $loader->load('config.yaml');
        $this->cacheContainer($container);
        return $container;
    }

    public function loadEnvironmentVariables()
    {
        $environment = new Environment();
        $filter = new Filter($this->preffix);
        $variables = $environment->getAll([$filter]);
        array_walk($variables, function (&$value, &$key) use ($filter) {
            $key = $filter->normalizeName($key);
            $value = $filter->transformFromJson($this->preffix);
        });
        return $variables;
    }

    protected function cacheContainer(Container $container): bool
    {
        if (!$container->getParameter('development')) {
            $container->compile();
            $item = $this->cache->getItem('definition.container');
            $item->set($container);
            $this->cache->save($item);
            return true;
        }
        return false;
    }

    protected function developmentSetup(Container $container)
    {
        if (!$container->getParameter('development')) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(-1);
        }
    }

    public function getApplication(): ApplicationInterface
    {
        return new Web($this);
    }
}
