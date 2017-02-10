<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\ControllerInformation;
use IainConnor\GameMaker\Endpoint;
use IainConnor\JabberJay\JabberJay;
use IainConnor\JabberJay\ResolvedRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class UnitTest extends Driver
{
    /**
     * Drive a request for the given inputs to the given endpoint by calling that method directly.
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
        $dummyRequest = new Request();
        $dummyRequest->attributes->set('_controller', $controller->class . "::" . $endpoint->method);

        $controllerResolver = new ControllerResolver();

        $resolvedRequest = new ResolvedRequest();
        $resolvedRequest->callableController = $controllerResolver->getController($dummyRequest);
        $resolvedRequest->controller = $controller;
        $resolvedRequest->endpoint = $endpoint;
        $resolvedRequest->callableInputs = $inputs;

        return $jabberJay->performResolvedRequest($resolvedRequest);
    }

}