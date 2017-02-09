<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\ControllerInformation;
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
     * @param JabberJay $jabberJay
     * @param ControllerInformation $controller
     * @param Endpoint $endpoint
     * @param array $inputs
     * @return Response
     */
    public abstract static function driveRequest(JabberJay $jabberJay, ControllerInformation $controller, Endpoint $endpoint, array $inputs);
}