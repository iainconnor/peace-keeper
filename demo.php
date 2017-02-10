<?php

include(dirname(__FILE__) . "/vendor/autoload.php");
include(dirname(__FILE__) . "/demo/Foo.php");
include(dirname(__FILE__) . "/demo/OutputWrapper.php");

$gameMaker = \IainConnor\GameMaker\GameMaker::instance();

// You should always set an AnnotationReader to improve performance.
// @see http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
$gameMaker->setAnnotationReader(
    new \IainConnor\Cornucopia\CachedReader(
        new \IainConnor\Cornucopia\AnnotationReader(),
        new \Doctrine\Common\Cache\ArrayCache()
    ));

// Parse controllers into a useable format.
$fooController = $gameMaker->parseController(Foo::class);

$strategy = new \CG\Core\DefaultGeneratorStrategy();

$peaceKeeper = new \IainConnor\PeaceKeeper\PeaceKeeper($gameMaker, new \IainConnor\PeaceKeeper\Drivers\UnitTest());

foreach ( $peaceKeeper->generateTestClassesForControllers($gameMaker->getParsedControllers()) as $class ) {
    $testClass = dirname(__FILE__) . DIRECTORY_SEPARATOR . "demo" . DIRECTORY_SEPARATOR . $class->getName() . ".php";

    file_put_contents($testClass, "<?php" . PHP_EOL . "include(\"" . dirname(__FILE__) . DIRECTORY_SEPARATOR . "demo" . DIRECTORY_SEPARATOR . "Foo.php\");" . PHP_EOL . "include(\"" . dirname(__FILE__) . DIRECTORY_SEPARATOR . "demo" . DIRECTORY_SEPARATOR . "OutputWrapper.php\");" . PHP_EOL . $strategy->generate($class));

    echo "Testing " . $class->getShortName() . PHP_EOL;
    exec(dirname(__FILE__)  . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "phpunit " . $testClass . " --bootstrap " . dirname(__FILE__)  . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php", $output);
    echo join(PHP_EOL, $output);
    echo PHP_EOL;
}