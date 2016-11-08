<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Service\Application;

use Symfony\Component\DependencyInjection\Container;

abstract class AbstractCommand
{
    /** @var Commandee */
    protected $commandee;

    /**
     * Command constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->commandee = $this->buildCommandee($container);
    }

    /**
     * @param Container $container
     * @return Commandee
     */
    abstract protected function buildCommandee(Container $container);
}
