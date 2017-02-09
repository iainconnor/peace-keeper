<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\ControllerInformation;
use IainConnor\GameMaker\Endpoint;
use IainConnor\JabberJay\JabberJay;
use Symfony\Component\HttpFoundation\Response;

class IntegrationTest extends Driver
{
    /**
     * Drive a request for the given inputs to the given endpoint.
     * Return that endpoint's response.
     *
     * @param JabberJay $jabberJay
     * @param ControllerInformation $controller
     * @param Endpoint $endpoint
     * @param array $inputs
     * @return Response
     */
    public static function driveRequest(JabberJay $jabberJay, ControllerInformation $controller, Endpoint $endpoint, array $inputs)
    {

        return $jabberJay->
    }

}