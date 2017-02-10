<?php


namespace IainConnor\PeaceKeeper;

use CG\Core\ClassUtils;
use CG\Generator\PhpClass;
use CG\Generator\PhpMethod;
use CG\Generator\PhpProperty;
use IainConnor\GameMaker\ControllerInformation;
use IainConnor\GameMaker\Endpoint;
use IainConnor\GameMaker\GameMaker;
use IainConnor\JabberJay\JabberJay;
use IainConnor\PeaceKeeper\Drivers\Driver;
use IainConnor\PeaceKeeper\Drivers\MethodCall;
use PhpParser\Builder\Method;

class PeaceKeeper
{
    /** @var GameMaker */
    protected $gameMaker;

    /** @var Driver */
    protected $requestDriver;

    /**
     * PeaceKeeper constructor.
     * @param GameMaker $gameMaker
     * @param Driver $requestDriver
     */
    public function __construct(GameMaker $gameMaker, Driver $requestDriver)
    {
        $this->gameMaker = $gameMaker;
        $this->requestDriver = $requestDriver;
    }

    /**
     * @param ControllerInformation[] $controllers
     * @return PhpClass[]
     */
    public function generateTestClassesForControllers(array $controllers) {

        return array_map([$this, "generateTestClassForController"], $controllers);
    }

    /**
     * @param ControllerInformation $controller
     * @return PhpClass
     */
    public function generateTestClassForController(ControllerInformation $controller) {
        $class = new PhpClass($controller->class . 'Test');
        $class->setParentClassName('PHPUnit\Framework\TestCase');

        $gameMakerProperty = new PhpProperty('gameMaker');
        $gameMakerProperty->setVisibility('protected');
        $gameMakerProperty->setStatic(true);

        $class->setProperty($gameMakerProperty);

        $jabberJayProperty = new PhpProperty('jabberJay');
        $jabberJayProperty->setVisibility('protected');
        $jabberJayProperty->setStatic(true);

        $class->setProperty($jabberJayProperty);

        $controllerProperty = new PhpProperty('controller');
        $controllerProperty->setVisibility('protected');
        $controllerProperty->setStatic(true);

        $class->setProperty($controllerProperty);

        $class->setMethod($this->generateSetupMethod($controller));

        foreach ( $controller->endpoints as $endpoint) {
            $class->setMethod($this->generateTestMethodForEndpoint($endpoint));
        }

        return $class;
    }

    public function generateSetupMethod(ControllerInformation $controller) {
        $method = new PhpMethod('setUpBeforeClass');
        $method->setStatic(true);

        $lines = [
            'static::$gameMaker = ' . GameMaker::class . '::instance();',
            'static::$controller = static::$gameMaker->parseController("' . addslashes($controller->class) . '");',
            'static::$jabberJay = ' . JabberJay::class . '::instance(static::$gameMaker);',
            'static::$jabberJay->addController(static::$controller);'
        ];

        $method->setBody(join(PHP_EOL, $lines));

        return $method;
    }

    public function generateTestMethodForEndpoint(Endpoint $endpoint) {
        $method = new PhpMethod('test' . ucfirst($endpoint->method));

        $lines = [
            '// Find endpoint to call.',
            '$endpoint = null;',
            'foreach ( static::$controller->endpoints as $searchEndpoint ) {',
            "\t" . 'if ( $searchEndpoint->httpMethod->path == "' . addslashes($endpoint->httpMethod->path) . '" ) {',
            "\t\t" . '$endpoint = $searchEndpoint;',
            "\t\t" . 'break;',
            "\t}",
            "}",
            '',
            '// Call endpoint.',
            '$response = ' . get_class($this->requestDriver) . '::driveRequest(static::$jabberJay, static::$controller, $endpoint, static::$jabberJay->getMockInputsForMethodForEndpoint($endpoint));',
            '',
            '// Get all the JSON schemas for the controller.',
            '$jsonSchemaProcessor = new \IainConnor\GameMaker\Processors\JsonSchema("' . addslashes($endpoint->method) . '");',
            '$jsonSchemas = $jsonSchemaProcessor->processController(static::$controller);',
            '',
            '// Locate the matching JSON schema.',
            '$foundValidSchema = false;',
            'foreach ( $endpoint->outputs as $output ) {',
            "\t" . '// Status codes have to match.',
            "\t" . 'if ( $response->getStatusCode() == $output->statusCode ) {',
            "\t\t" . 'foreach ( $output->typeHint->types as $type ) {',
            "\t\t\t" . '// JSON schema for output type has to exist.',
            "\t\t\t" . '$jsonSchemaKey = $type->type == \IainConnor\Cornucopia\Annotations\TypeHint::ARRAY_TYPE ? ($type->genericType . \IainConnor\Cornucopia\Annotations\TypeHint::ARRAY_TYPE_SHORT) : $type->type;',
            "\t\t\t" . 'if ( array_key_exists($jsonSchemaKey, $jsonSchemas) ) {',
            "\t\t\t\t" . '$jsonSchema = $jsonSchemas[$jsonSchemaKey];',
            "\t\t\t\t" . '$validator = new JsonSchema\Validator();',
            "\t\t\t\t" . '$validator->check(json_decode($response->getContent()), $jsonSchema);',
            "\t\t\t\t" . 'if ( $validator->isValid() ) {',
            "\t\t\t\t\t" . '$foundValidSchema = true;',
            "\t\t\t\t\t" . 'break(2);',
            "\t\t\t\t}",
            "\t\t\t" . '} else if ( $type->type == null && $response->getContent() == null ) {',
            "\t\t\t\t" . '$foundValidSchema = true;',
            "\t\t\t\t" . 'break(2);',
            "\t\t\t}",
            "\t\t}",
            "\t}",
            "}",
            '',
            'if ( !$foundValidSchema ) {',
            "\t" . 'fwrite(STDERR, print_r($response, TRUE));',
            '}',
            '',
            '$this->assertTrue($foundValidSchema);'
        ];

        $method->setBody(join(PHP_EOL, $lines));

        return $method;
    }

}