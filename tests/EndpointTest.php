<?php

namespace Microsoft\Luis\Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Microsoft\Luis\Endpoint;

class EndpointTest extends TestCase
{
    private $endpoint;

    protected function setUp()
    {
        $this->endpoint = new Endpoint([
            'location' => getenv('LUIS_LOCATION'),
            'appId' => getenv('LUIS_APP_ID'),
            'versionId' => getenv('LUIS_APP_VERSION_ID'),
            'endpointKey' => getenv('LUIS_ENDPOINT_KEY'),
            'timeout' => 10,
        ]);
    }

    private function search($q)
    {
        error_log('q length: ' . mb_strlen($q));
        $query = ['q' => $q];
        $data = $this->endpoint->search($query);
        error_log('search result: ' . var_export($data, true));
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('query', $data);
        if (isset($data['topScoringIntent'])) {
            $this->assertArrayHasKey('intent', $data['topScoringIntent']);
            $this->assertArrayHasKey('score', $data['topScoringIntent']);
        }
        $this->assertArrayHasKey('entities', $data);
    }

    public function testSearch()
    {
        $q = 'hello';
        $this->search($q);
    }

    public function testSearchLongEnglish()
    {
        $q = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.';
        $this->search($q);
    }
}
