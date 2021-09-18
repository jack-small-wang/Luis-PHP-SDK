<?php

namespace Microsoft\Luis\Management;

class App extends Base
{
    public function __construct($conf)
    {
        parent::__construct($conf);
    }

    protected $validations = [
        'store' => [
            'name' => 'required|string',
            'culture' => 'required|string',
            'description' => 'string',
            'usageScenario' => 'string',
            'domain' => 'string',
            'initialVersionId' => 'numeric',
        ],
    ];

    public function select($appId)
    {
        $this->conf['appId'] = $appId;
        return new AppModel($this->conf);
    }
}
