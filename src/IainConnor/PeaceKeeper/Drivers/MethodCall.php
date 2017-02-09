<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\Annotations\Input;
use IainConnor\GameMaker\Endpoint;

class MethodCall extends Driver
{
    public function driveRequest(Endpoint $endpoint, array $inputs = [])
    {
        return call_user_func_array($endpoint->method, $this->generateInputValues($endpoint->inputs));
    }

    /**
     * @param Input[] $inputs
     * @param string[] $existingInputs
     * @return array
     */
    protected function generateInputValues(array $inputs, array $existingInputs = []) {
        $inputValues = [];

        foreach ( $inputs as $input ) {
            if ( array_key_exists($input->variableName, $existingInputs) ) {
                $inputValues[] = $existingInputs[$input->variableName];
            } else {

            }
        }

        return $inputValues;
    }

}