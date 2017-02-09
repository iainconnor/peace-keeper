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
use IainConnor\PeaceKeeper\Drivers\MethodCall;
use PhpParser\Builder\Method;

class PeaceKeeper
{
    /** @var GameMaker */
    protected $gameMaker;

    const TEST_TYPE_ACCEPTANCE = "acceptance";
    const TEST_TYPE_INTEGRATION = "integration";
    const TEST_TYPE_UNIT = "unit";

    /**
     * PeaceKeeper constructor.
     * @param GameMaker $gameMaker
     */
    public function __construct(GameMaker $gameMaker)
    {
        $this->gameMaker = $gameMaker;
    }

    public function generateTestClassesForControllers(array $controllers) {

        return array_map([$this, "generateTestClassForController"], $controllers);
    }

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

        $class->setMethod($this->generateSetupMethod($controller));

        foreach ( $controller->endpoints as $endpoint) {
            $class->setMethod($this->generateTestMethodForEndpoint($endpoint));
        }

        return $class;
    }

    public function generateSetupMethod(ControllerInformation $information) {
        $method = new PhpMethod('setUpBeforeClass');
        $method->setStatic(true);

        $lines = [
            'static::$gameMaker = ' . GameMaker::class . '::instance();',
            '$controller = static::$gameMaker->parseController("' . addslashes($information->class) . '"");',
            'static::$jabberJay = ' . JabberJay::class . '::instance(static::$gameMaker);',
            'static::$jabberJay->addController($controller)'
        ];

        $method->setBody(join(PHP_EOL, $lines));

        return $method;
    }

    public function generateTestMethodForEndpoint(Endpoint $endpoint) {
        $method = new PhpMethod('test' . ucfirst($endpoint->method));

        $lines = [
            'static::$gameMaker = ' . GameMaker::class . '::instance();',
            '$controller = static::$gameMaker->parseController("' . addslashes($information->class) . '"");',
            'static::$jabberJay = ' . JabberJay::class . '::instance(static::$gameMaker);',
            'static::$jabberJay->addController($controller)'
        ];

        $method->setBody(join(PHP_EOL, $lines));

        return $method;
    }

}