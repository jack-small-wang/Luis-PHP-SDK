<?php

namespace Microsoft\Luis\Management;

use Microsoft\Luis\Base as LuisBase;

abstract class Base extends LuisBase
{
    protected $apiPath = 'api/v2.0/apps/';

    protected $conf = [
        'location' => 'westus',
        'programmaticApiKey' => '',
    ];
    protected $validations = [];

    public function index($skip = null, $take = null)
    {
        $data = [
            'skip' => $skip,
            'take' => $take,
        ];
        return $this->paginate($this->requestForJson('GET', '', ['query' => $data]));
    }

    public function store($data)
    {
        if (isset($this->validations['store'])) {
            $this->validate($data, $this->validations['store']);
        }
        return [
            'id' => $this->requestForString('POST', '', ['json' => $data]),
        ];
    }

    public function destroy($id)
    {
        $this->client->request('DELETE', strval($id));
        return true;
    }

    public function show($id)
    {
        return $this->requestForJson('GET', strval($id));
    }
}
