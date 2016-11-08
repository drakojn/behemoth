<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Service\Application\Web\Request;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use Symfony\Component\HttpFoundation\Request;

class Event extends SymfonyEvent
{
    protected $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest():Request
    {
        return $this->request;
    }
}
