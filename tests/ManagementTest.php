<?php

namespace Microsoft\Luis\Tests\Management;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Microsoft\Luis\Management;

class ManagementTest extends TestCase
{
    private $management;

    protected function setUp()
    {
        $this->management = new Management([
            'programmaticApiKey' => getenv('LUIS_PROGRAMMATIC_API_KEY'),
            'timeout' => 10,
        ]);
    }

    public function testIndex()
    {
        $r = $this->management->apps->index();
        $this->assertArrayHasKey('data', $r);
        $data = $r['data'];
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertArrayHasKey('endpoints', $data[0]);
        $this->assertArrayHasKey('activeVersion', $data[0]);
    }

    public function testStoreShowAndDelete()
    {
        $data = [
            'name' => 'phpunit-' . time(),
            'culture' => 'en-us',
        ];
        $r = $this->management->apps->store($data);
        error_log('apps->store result:' . var_export($r, true));
        $this->assertNotEmpty($r);
        $this->assertTrue(is_string($r['id']));

        $r1 = $this->management->apps->show($r['id']);
        error_log('apps->show result:' . var_export($r1, true));
        $this->assertNotEmpty($r1);
        $this->assertArrayHasKey('id', $r1);
        $this->assertArrayHasKey('name', $r1);
        $this->assertArrayHasKey('endpoints', $r1);

        $r2 = $this->management->apps->destroy($r['id']);
        $this->assertTrue($r2);

        try {
            $r2 = $this->management->apps->destroy($r['id']);
            $this->assertTrue($r2);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(400, $e->getCode());
        }
    }
}
