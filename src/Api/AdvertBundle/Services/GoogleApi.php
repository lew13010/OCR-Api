<?php
/**
 * Created by PhpStorm.
 * User: Lew
 * Date: 07/10/2016
 * Time: 14:17
 */

namespace Api\AdvertBundle\Services;


class GoogleApi
{
    private $key;

    /**
     * GoogleApi constructor.
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }


    public function verifCityFrance($name)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($name).'&key='.$this->key;
        $result = file_get_contents($url);
        $reponse = json_decode($result);
        if($reponse->results[0]->address_components[3]->long_name == 'France'){
            return true;
        }
    }
}