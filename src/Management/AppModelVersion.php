<?php

namespace Microsoft\Luis\Management;

class AppModelVersion extends Base
{
    protected $basePath = '{appId}/versions/';

    protected $conf = [
        'appId' => '',
        'programmaticApiKey' => '',
    ];

    protected $validations = [
        'import' => [
            'name' => 'required|string',
            'culture' => 'required|string',
        ],
    ];

    public function select($versionId)
    {
        $this->conf['versionId'] = $versionId;
        return new AppModelVersionModel($this->conf);
    }

    public function import($data, $versionId = null)
    {
        $this->validate($data, $this->validations['import']);
        $path = !empty($versionId) ? 'import?versionId=' . $versionId : 'import';
        return [
            'versionId' => $this->requestForString('POST', $path, ['json' => $data]),
        ];
    }
}
