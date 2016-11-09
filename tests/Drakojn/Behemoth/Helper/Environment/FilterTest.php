<?php
namespace Drakojn\Behemoth\Helper\Environment;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Filter */
    protected $object;

    public function setUp()
    {
        $this->object = new Filter('APP');
    }

    public function tearDown()
    {
        $this->object = null;
    }

    public function testFilter()
    {
        $base = [
            'APP_DB' => 'sqlite::memory:',
            'APP_TMP_DIR' => '/tmp',
            'PATH' => get_include_path()
        ];
        $result = $this->object->filter($base);
        static::assertArrayNotHasKey('PATH', $result);
    }

    public function testCallback()
    {
        $condition = $this->object->callback('APP_DB');
        static::assertTrue($condition);
    }
    public function testNormalizeName()
    {
        $result = $this->object->normalizeName('APP_TMP_DIR');
        static::assertEquals('app.tmp.dir', $result);
    }
    public function testTransformFromJson()
    {
        $result = $this->object->transformFromJson('{"id":123}');
        static::assertObjectHasAttribute('id', $result);
        $result = $this->object->transformFromJson('["A","B"]');
        static::assertArrayHasKey(0, $result);
        $result = $this->object->transformFromJson('TEST');
        static::assertEquals('TEST', $result);
    }
}
