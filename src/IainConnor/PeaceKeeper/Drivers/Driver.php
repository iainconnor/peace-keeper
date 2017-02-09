<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\Endpoint;
use IainConnor\GameMaker\GameMaker;
use IainConnor\JabberJay\JabberJay;
use Symfony\Component\HttpFoundation\Response;

abstract class Driver
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
    public abstract static function driveRequest(Endpoint $endpoint, array $inputs, JabberJay $jabberJay);
}