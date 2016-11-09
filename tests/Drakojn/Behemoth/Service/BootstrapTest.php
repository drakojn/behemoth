<?php
namespace Drakojn\Behemoth\Service;

use Symfony\Component\Cache\Adapter\ArrayAdapter;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Bootstrap */
    protected $object;
    protected $applicationPath;

    public function setUp()
    {
        $this->applicationPath = realpath(__DIR__ . '/../../../../');
        if (!is_dir($this->applicationPath . '/definitions')) {
            mkdir($this->applicationPath . '/definitions');
        }
        $cache = new ArrayAdapter();
        $this->object = new Bootstrap($this->applicationPath, $cache, 'APP');
    }

    public function tearDown()
    {
        rmdir($this->applicationPath . '/definitions');
        $this->object = null;
    }

    public function testGetApplicationPath()
    {
        $path = $this->object->getApplicationPath();
        static::assertDirectoryExists($path);
    }
}
