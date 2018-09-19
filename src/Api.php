<?php

namespace HoneybadgerIo\NovaHoneybadger;

use GuzzleHttp\Client;

class Api
{
    const API_URL = 'https://app.honeybadger.io/v2/';

    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getFaults($projectId, $searchString = null, $limit = 10, $order = 'recent')
    {
        $response = $this->client->request('GET', 'projects/' . $projectId . '/faults', [
            'query' => [
                'q' => $searchString,
                'limit' => $limit,
                'order' => $order
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function get($url)
    {
        $response = $this->client->request('GET', $url);

        return json_decode($response->getBody()->getContents(), true);
    }

}