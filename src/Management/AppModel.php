<?php

namespace Microsoft\Luis\Management;

class AppModel extends Base
{
    protected $basePath = '{appId}/';

    protected $conf = [
        'appId' => '',
        'programmaticApiKey' => '',
    ];

    public $id = null;
    public $versions = null;

    public function __construct($conf)
    {
        parent::__construct($conf);
        $this->id = $conf['appId'];
        if (is_null($this->versions)) {
            $this->versions = new AppModelVersion($conf);
        }
    }

    public function version($versionId)
    {
        return $this->versions->select($versionId);
    }

    public function publish($versionId, $isStaging = false)
    {
        return $this->requestForJson('POST', 'publish', ['json' => [
            'versionId' => $versionId,
            'isStaging' => $isStaging,
        ]]);
    }
}
