<?php

namespace Vitorschweder\Xlr8;

use GuzzleHttp\Client;
use Location\Coordinate;
use Location\Distance\Haversine;

class Search
{
    const END_POINTS = [
        'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_1.json',
        'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json',
    ];
    
    public static function getNearbyHotels($latitude, $longitude, $orderBy = 'proximity')
    {
        // Create instance of GuzzleHttp Client
        $client = new Client;

        $result = [];
        foreach (self::END_POINTS as $endpoint) {
            $response = $client->get($endpoint)->getBody()->getContents();
            $result[] = json_decode($response, true);
        }

        /**
         * These classes Coordinate and Haversine are used to calculate distance by 
         * latitude and longitude
         */ 
        $origin = new Coordinate($latitude, $longitude);
        $haversine = new Haversine();

        $items = [];
        foreach ($result as $data) {
            foreach ($data['message'] as $line) {
                $coordinates = new Coordinate((float) $line[1], (float) $line[2]);
                $distanceCoordinates = $haversine->getDistance($origin, $coordinates);

                $items[] = [
                    'name' => $line[0],
                    'latitude' => $line[1],
                    'longitude' => $line[2],
                    'price' => $line[3],
                    'distanceCoordinates' => $distanceCoordinates,
                    'distanceKm' => self::convertToKm($latitude, $longitude, (float) $line[1], (float) $line[2])
                ];
            }
        }

        if ($orderBy == 'proximity') {
            array_multisort(array_column($items, 'distanceCoordinates'), SORT_ASC, $items);
        } else {
            array_multisort(array_column($items, 'price'), SORT_ASC, $items);
        }

        // Show list with formatted data
        $finalData = '<ul>';
        foreach ($items as $item) {
            $finalData .= '<li>'.$item['name'].', '.$item['distanceKm'].' KM, '.$item['price'].' EUR</li>';

        }
        $finalData .= '<ul>';

        echo $finalData;
    }

    /**
     * This method convert distance from latitudes and longitudes to KM
     * 
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     */
    public static function convertToKm($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $latitudeFrom = deg2rad($latitudeFrom);
        $longitudeFrom = deg2rad($longitudeFrom);
        $latitudeTo = deg2rad($latitudeTo);
        $longitudeTo = deg2rad($longitudeTo);
        
        $distance = (6371 * acos( cos( $latitudeFrom ) * cos( $latitudeTo ) * cos( $longitudeTo - $longitudeFrom ) + sin( $latitudeFrom ) * sin($latitudeTo) ) );
        $distance = number_format($distance, 2, '.', '');

        return $distance;
    }
}