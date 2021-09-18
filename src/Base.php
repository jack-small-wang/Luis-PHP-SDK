<?php

namespace Microsoft\Luis;

use GuzzleHttp\Client;

abstract class Base
{
    const API_ROOT_URI = 'https://{location}.api.cognitive.microsoft.com/luis/';
    protected $apiPath;
    protected $basePath;
    protected $client;
    protected $conf = [];

    public function __construct($conf)
    {
        $this->setConf($conf);
    }

    public function setConf($conf)
    {
        $this->conf = array_merge($this->conf, $conf);
        $baseUri = self::API_ROOT_URI . $this->apiPath . $this->basePath;
        foreach ($this->conf as $k => $v) {
            if (empty($v)) {
                throw new \Exception('bad args, required: ' . $k, 400);
            }
            $baseUri = str_replace('{' . $k . '}', $v, $baseUri);
        }
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => isset($this->conf['timeout']) ? $this->conf['timeout'] : 3,
            'headers' => [
                'Accept' => 'application/json',
                'Ocp-Apim-Subscription-Key' => !empty($this->conf['endpointKey']) ? $this->conf['endpointKey'] : $this->conf['programmaticApiKey'],
            ],
        ]);
    }

    protected function request($method, $path, $body = [])
    {
        try {
            $response = $this->client->request($method, $path, $body);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $reason = $response->getReasonPhrase();
            $code = $e->getCode();
            $msg = $response->getBody()->getContents();
            $tmp = json_decode($response->getBody()->getContents(), true);
            if (isset($tmp['error']['code']) && isset($tmp['error']['message'])) {
                $msg = $tmp['error']['code'] . ': ' . $tmp['error']['message'];
            }
            throw new \Exception($msg, $code);
        }
        return $response->getBody()->getContents();
    }

    protected function requestForJson($method, $path, $body = [])
    {
        $r = $this->request($method, $path, $body);
        return json_decode($r, true);
    }

    protected function requestForString($method, $path, $body = [])
    {
        // this api return text not json...ms is rubbish
        $r = $this->request($method, $path, $body);
        // warning: this text start and end with double quotes, should trim
        return trim($r, '"');
    }

    protected function paginate($data)
    {
        return [
            'data' => $data,
        ];
    }

    protected function validate($data, $rules)
    {
        foreach ($rules as $column => $rule) {
            $tmp = explode('|', $rule);
            if (in_array('required', $tmp)) {
                if (!isset($data[$column])) {
                    throw new \Exception('bad args, required: ' . $column, 400);
                }
            }
        }
    }
}
