<?php


namespace IainConnor\PeaceKeeper;

use CG\Core\ClassUtils;
use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use IainConnor\GameMaker\ControllerInformation;
use IainConnor\GameMaker\Endpoint;
use IainConnor\PeaceKeeper\Drivers\MethodCall;
use PhpParser\Builder\Method;

class PeaceKeeper
{
    public function generateTestClassesForControllers(array $controllers) {

        return array_map([$this, "generateTestsForController"], $controllers);
    }

    public function generateTestClassForController(ControllerInformation $controller) {
        $class = new PhpClass($controller->class . 'Test');
        $class->setParentClassName('PHPUnit\Framework\TestCase');

        foreach ( $controller->endpoints as $endpoint) {
            $class->setMethod($this->generateTestMethodForEndpoint($endpoint));
        }

        return $class;
    }

    public function generateTestMethodForEndpoint(Endpoint $endpoint) {
        $method = new PhpMethod('test' . ucfirst($endpoint->method));



        return $method;
    }

    public function fooBar(Endpoint $endpoint) {
        $driver = new MethodCall();
        $response = $driver->driveRequest($endpoint);

    }

}