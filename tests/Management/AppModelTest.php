<?php

namespace Microsoft\Luis\Tests\Management;

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Microsoft\Luis\Management;
use Microsoft\Luis\Management\AppModel;
use Faker;

class AppModelTest extends TestCase
{
    private static $app;
    private static $appVersion;
    private static $faker;
    private static $management;

    public static function setUpBeforeClass()
    {
        error_log(__METHOD__);
        self::$faker = Faker\Factory::create();
        self::$management = new Management([
            'programmaticApiKey' => getenv('LUIS_PROGRAMMATIC_API_KEY'),
            'timeout' => 20,
        ]);
        $data = [
            'name' => 'phpunit-' . time(),
            'culture' => 'en-us',
        ];
        $r = self::$management->apps->store($data);
        error_log('apps->store result: ' . var_export($r, true));
        $app = self::$management->apps->show($r['id']);
        self::$app = self::$management->app($app['id']);
        self::$appVersion = self::$app->version($app['activeVersion']);
    }

    public function testBadConf()
    {
        try {
            new AppModel([
                'appId' => '',
                'programmaticApiKey' => '',
            ]);
        } catch (\Exception $e) {
            $this->assertEquals('bad args, required: appId', $e->getMessage());
        }
        try {
            new AppModel([
                'appId' => 'asdf',
                'programmaticApiKey' => '',
            ]);
        } catch (\Exception $e) {
            $this->assertEquals('bad args, required: programmaticApiKey', $e->getMessage());
        }
    }

    public function testIntentStoreShowIndexAndDestory()
    {
        sleep(1);
        $data = [
            'name' => self::$faker->word,
        ];
        $r = self::$appVersion->intents->store($data);
        error_log('intents->store result: ' . var_export($r, true));
        $this->assertTrue(is_string($r['id']));

        $r1 = self::$appVersion->intents->show($r['id']);
        error_log('intents->show result:' . var_export($r1, true));
        $this->assertArrayHasKey('id', $r1);
        $this->assertArrayHasKey('name', $r1);
        $this->assertArrayHasKey('typeId', $r1);
        $this->assertArrayHasKey('readableType', $r1);

        $r2 = self::$appVersion->intents->index();
        error_log('intents->index result: ' . var_export($r2, true));
        $data = $r2['data'];
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertArrayHasKey('typeId', $data[0]);
        $this->assertArrayHasKey('readableType', $data[0]);

        $tmp = self::$appVersion->train($r['id']);
        $this->assertTrue($tmp);

        $data = self::$appVersion->getTrainingStatus($r['id']);
        error_log('app->getTrainingStatus result: ' . var_export($data, true));
        $this->assertArrayHasKey('modelId', $data[0]);
        $this->assertArrayHasKey('details', $data[0]);
        $this->assertArrayHasKey('statusId', $data[0]['details']);
        $this->assertArrayHasKey('status', $data[0]['details']);
        $this->assertArrayHasKey('exampleCount', $data[0]['details']);

        try {
            $tmp = self::$app->publish(self::$appVersion->versionId);
            $this->assertArrayHasKey('endpointUrl', $tmp);
            $this->assertArrayHasKey('subscription-key', $tmp);
            $this->assertArrayHasKey('endpointRegion', $tmp);
            $this->assertArrayHasKey('isStaging', $tmp);
        } catch (\Exception $e) {
            echo "\n" . 'app->publish exception: ' . $e->getMessage() . "\n";
            $this->assertEquals(400, $e->getCode());
            $msg = json_decode($e->getMessage(), true);
            $expectMsg = [
                'error' => [
                    'code' => 'BadArgument',
                    'message' => 'Application cannot be published. One or more models require training.',
                ]
            ];
            $this->assertEquals($expectMsg, $msg);
        }

        $r3 = self::$appVersion->intents->destroy($r['id']);
        $this->assertTrue($r3);

        try {
            $r4 = self::$appVersion->intents->destroy($r['id']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testEntityStoreShowIndexAndDestory()
    {
        sleep(1);
        $data = [
            'name' => self::$faker->word,
        ];
        $r = self::$appVersion->entities->store($data);
        error_log('entities->store result: ' . var_export($r, true));
        $this->assertTrue(is_string($r['id']));

        $r1 = self::$appVersion->entities->show($r['id']);
        error_log('entities->show result:' . var_export($r1, true));
        $this->assertArrayHasKey('id', $r1);
        $this->assertArrayHasKey('name', $r1);
        $this->assertArrayHasKey('typeId', $r1);
        $this->assertArrayHasKey('readableType', $r1);

        $r2 = self::$appVersion->entities->index();
        error_log('entities->index result: ' . var_export($r2, true));
        $data = $r2['data'];
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertArrayHasKey('typeId', $data[0]);
        $this->assertArrayHasKey('readableType', $data[0]);

        $r3 = self::$appVersion->entities->destroy($r['id']);
        $this->assertTrue($r3);

        try {
            $r4 = self::$appVersion->entities->destroy($r['id']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function testExampleStoreShowIndexAndDestory()
    {
        sleep(1);
        $intent_name = self::$faker->word;
        $data = [
            'name' => $intent_name,
        ];
        $r = self::$appVersion->intents->store($data);
        error_log('intents->store name: ' . $intent_name);
        error_log('intents->store result: ' . var_export($r, true));
        $this->assertTrue(is_string($r['id']));

        $text1 = strtolower(self::$faker->sentence);
        $text2 = strtolower(self::$faker->sentence);
        $texts = [$text1, $text2];
        error_log('examples texts: ' . var_export($texts, true));
        $data = [];
        foreach ($texts as $text) {
            $data[] = [
                'text' => $text,
                'intentName' => $intent_name,
            ];
        }
        $r = self::$appVersion->examples->batchAddLabels($data);
        error_log('examples->batchAddLabels result: ' . var_export($r, true));
        $this->assertEquals(count($texts), count($r));
        foreach ($r as $one) {
            $this->assertArrayHasKey('ExampleId', $one['value']);
            $this->assertTrue(in_array($one['value']['UtteranceText'], $texts));
            $this->assertFalse($one['hasError']);
        }

        $r2 = self::$appVersion->examples->index();
        error_log('examples->index result: ' . var_export($r2, true));
        $data = $r2['data'];
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('text', $data[0]);
        $this->assertArrayHasKey('tokenizedText', $data[0]);
        $this->assertEquals($intent_name, $data[0]['intentLabel']);

        $r3 = self::$appVersion->examples->destroy($data[0]['id']);
        $this->assertTrue($r3);
    }

    public function testVersionCloneToNew()
    {
        sleep(1);
        $version = self::$faker->randomFloat(1, 0.2, 1000);
        $r = self::$appVersion->cloneToNew($version);
        error_log('appVersion->cloneToNew result: ' . var_export($r, true));
        $this->assertEquals($version, $r['versionId']);
    }

    public function testImportVersion()
    {
        sleep(1);
        $versionId = self::$faker->randomFloat(1, 0.2, 1000);
        $data = [
            "luis_schema_version" => "1.3.1",
            "name" => "DummyImportedApp",
            "versionId" => "0.2",
            "desc" => "This is my dummy imported application",
            "culture" => "en-us",
            "intents" => [
                [
                    "name" => "BookFlight"
                ],
                [
                    "name" => "GetWeather"
                ],
                [
                    "name" => "None"
                ]
            ],
            "entities" => [
                [
                    "name" => "Location",
                    "children" => [
                        "To",
                        "From"
                    ]
                ]
            ],
            "composites" => [

            ],
            "closedLists" => [

            ],
            "bing_entities" => [
                "datetimeV2"
            ],
            "actions" => [

            ],
            "model_features" => [
                [
                    "name" => "Cities",
                    "mode" => true,
                    "words" => "Seattle,New York,Paris,Moscow,Beijin",
                    "activated" => true
                ]
            ],
            "regex_features" => [

            ],
            "utterances" => [
                [
                    "text" => "book me a flight from redmond to new york next saturday",
                    "intent" => "BookFlight",
                    "entities" => [
                        [
                            "entity" => "Location::From",
                            "startPos" => 5,
                            "endPos" => 5
                        ],
                        [
                            "entity" => "Location::To",
                            "startPos" => 7,
                            "endPos" => 8
                        ]
                    ]
                ],
                [
                    "text" => "what's the weather like in paris?",
                    "intent" => "GetWeather",
                    "entities" => [
                        [
                            "entity" => "Location",
                            "startPos" => 7,
                            "endPos" => 7
                        ]
                    ]
                ]
            ]
        ];
        $r = self::$app->versions->import($data, $versionId);
        error_log('app->versions->import result: ' . var_export($r, true));
        $this->assertEquals($versionId, $r['versionId']);
    }

    public static function tearDownAfterClass()
    {
        sleep(1);
        error_log(__METHOD__);
        self::$management->apps->destroy(self::$app->id);
    }
}
