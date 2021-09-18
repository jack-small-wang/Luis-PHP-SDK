<?php

namespace Microsoft\Luis\Management;

class AppModelEntity extends Base
{
    protected $basePath = '{appId}/versions/{versionId}/entities/';

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
