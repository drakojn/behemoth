<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Service\Application;

use Drakojn\Behemoth\Service\Bootstrap;
use Symfony\Component\DependencyInjection\Container;

interface ApplicationInterface
{
    public function getContainer():Container;

    public function getBootstrap():Bootstrap;

    public function __invoke():string;
}
