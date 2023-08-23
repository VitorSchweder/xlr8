<?php

namespace Vitorschweder\Xlr8;

class Search
{
    const BASE_ENDPOINT = 'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com';
    
    public function __construct(
        private Client $client = new Client
    ){}

    public static function getNearbyHotels($latitude, $longitude, $orderBy = 'proximity')
    {
        for ($i=1;$i<=2;$i++) {
            $response = $this->client->get(self::BASE_ENDPOINT.'/source_'+$i+'.json')
                                     ->getBody()
                                     ->getContents();
            $result[] = json_decode($response, true);
        }

        foreach ($result as $data) {
            print_r($data);
        }
    }
}