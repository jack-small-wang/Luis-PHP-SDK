<?php

namespace Microsoft\Luis;

use Microsoft\Luis\Base as LuisBase;
use Microsoft\Luis\Management\App;
use Microsoft\Luis\Management\Model;

class Management extends LuisBase
{
    protected $apiPath = 'api/v2.0/apps/';

    protected $conf = [
        'location' => 'westus',
        'programmaticApiKey' => '',
    ];

    public $apps = null;

    public function __construct($conf)
    {
        parent::__construct($conf);
        if (is_null($this->apps)) {
            $this->apps = new App($this->conf);
        }
    }

    public function app($appId)
    {
        return $this->apps->select($appId);
    }
}
