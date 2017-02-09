<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\Endpoint;
use IainConnor\JabberJay\JabberJay;

class UnitTest extends Driver
{
    /**
     * Drive a request for the given inputs to the given endpoint.
     * Return that endpoint's response.
     *
     * @param Endpoint $endpoint
     * @param array $inputs
     * @param JabberJay $jabberJay
     * @return Response
     */
    public static function driveRequest(Endpoint $endpoint, array $inputs, JabberJay $jabberJay)
    {

        return $jabberJay->
    }

}