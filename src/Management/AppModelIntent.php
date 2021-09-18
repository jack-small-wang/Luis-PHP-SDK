<?php

namespace Microsoft\Luis\Management;

class AppModelIntent extends Base
{
    protected $basePath = '{appId}/versions/{versionId}/intents/';

    protected $conf = [
        'appId' => '',
        'versionId' => '',
        'programmaticApiKey' => '',
    ];

    protected $validations = [
        'store' => [
            'name' => 'required|string',
        ],
    ];
}
