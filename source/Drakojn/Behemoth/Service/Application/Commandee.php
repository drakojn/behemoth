<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Service\Application;

use Symfony\Component\DependencyInjection\Container;

class Commandee
{
    /** @var  Container */
    protected $container;

    /**
     * Commandee constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
