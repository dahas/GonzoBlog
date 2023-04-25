<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Gonzo\Sources\Cache;

class CacheTest extends TestCase {

    private Cache $cache;

    private array $data = [
        "fruits" => ["Lemon", "Apple", "Cherry", "Orange"],
        "formulaone" => ["Senna", "Schumi", "Lauda", "Prost"],
        "cities" => ["London", "New York", "Rio de Janiero", "Shanghai"],
        "values" => ["random", 2.300, true, 55]
    ];

    protected function setUp(): void
    {
        $this->cache = new Cache(__DIR__ . '/cache/data');
    }

    protected function tearDown(): void
    {
        unset($this->cache);
    }

    public function testException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cache->get('qwert44');
    }

    public function testSetMultiple()
    {
        $res = $this->cache->setMultiple($this->data);
        $this->assertTrue($res);
    }

    public function testGetMultiple()
    {
        $res = $this->cache->getMultiple(["fruits", "values"]);
        $this->assertEquals(
            [
                "fruits" => $this->data["fruits"],
                "values" => $this->data["values"]
            ],
            $res
        );
    }

    public function testDeleteMultiple()
    {
        $res = $this->cache->deleteMultiple(["formulaone", "cities"]);
        $this->assertTrue($res);
    }

    public function testHas()
    {
        $res = $this->cache->has("cities");
        $this->assertFalse($res);
    }

    public function testClear()
    {
        $res = $this->cache->clear();
        $this->assertTrue($res);
    }

    public function testGet()
    {
        $res = $this->cache->get("fruits", "Sorry");
        $this->assertEquals("Sorry", $res);
    }
}