<?php

namespace Microsoft\Luis\Management;

class AppModelVersionModel extends Base
{
    protected $basePath = '{appId}/versions/{versionId}/';

    protected $conf = [
        'appId' => '',
        'programmaticApiKey' => '',
    ];

    public $appId;
    public $versionId;
    public $intents = null;
    public $entities = null;
    public $examples = null;

    public function __construct($conf)
    {
        parent::__construct($conf);
        $this->appId = $conf['appId'];
        $this->versionId = $conf['versionId'];
        if (is_null($this->intents)) {
            $this->intents = new AppModelIntent($conf);
        }
        if (is_null($this->entities)) {
            $this->entities = new AppModelEntity($conf);
        }
        if (is_null($this->examples)) {
            $this->examples = new VersionModelExample($conf);
        }
    }

    public function train()
    {
        $this->requestForJson('POST', 'train');
        return true;
    }

    public function getTrainingStatus()
    {
        return $this->requestForJson('GET', 'train');
    }

    public function cloneToNew($newVersion)
    {
        $r = $this->requestForString('POST', 'clone', ['json' => [
            'version' => $newVersion,
        ]]);
        return [
            'versionId' => $r,
        ];
    }
}
