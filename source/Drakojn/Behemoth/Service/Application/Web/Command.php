<?php
declare(strict_types=1);
namespace Drakojn\Behemoth\Service\Application\Web;

use Drakojn\Behemoth\Service\Application\AbstractCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Command extends AbstractCommand
{
    abstract public function execute(Request $request, array $attributes = []):Response;
}
