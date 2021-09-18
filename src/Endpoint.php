<?php

namespace Microsoft\Luis;

class Endpoint extends Base
{
    protected $apiPath = 'v2.0/apps/';
    protected $basePath = '{appId}';

    protected $conf = [
        'location' => '',
        'appId' => '',
        'endpointKey' => '',
    ];

    /**
     * Get predictions from endpoint
     *
     * @example shell curl -v -X GET "https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/{appId}?q={q}?timezoneOffset={number}&verbose={boolean}&spellCheck={boolean}&staging={boolean}" -H "Ocp-Apim-Subscription-Key: {subscription key}"
     * @link https://westus.dev.cognitive.microsoft.com/docs/services/5819c76f40a6350ce09de1ac/operations/5819c77140a63516d81aee78
     * @link https://westus.dev.cognitive.microsoft.com/docs/services/5819c76f40a6350ce09de1ac/operations/5819c77140a63516d81aee79
     * @return array
     */
    public function search($query)
    {
        // The current maximum query size is 500 characters.
        if (mb_strlen($query['q']) >= 500) {
            $query['q'] = mb_substr($query['q'], 0, 500);
        }
        return $this->requestForJson('GET', '', ['query' => $query]);
    }
}
