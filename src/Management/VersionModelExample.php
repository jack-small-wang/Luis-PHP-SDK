<?php

namespace Microsoft\Luis\Management;

class VersionModelExample extends Base
{
    protected $basePath = '{appId}/versions/{versionId}/examples/';

    protected $conf = [
        'appId' => '',
        'versionId' => '',
        'programmaticApiKey' => '',
    ];

    /**
     * examples - Batch add labels
     * @link https://westus.dev.cognitive.microsoft.com/docs/services/5890b47c39e2bb17b84a55ff/operations/5890b47c39e2bb052c5b9c09
     */
    public function batchAddLabels($data)
    {
        return $this->requestForJson('POST', '', ['json' => $data]);
    }

    public function show($id)
    {
        return false;
    }
 
    public function store($data)
    {
        return false;
    }
}
