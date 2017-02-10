<?php


namespace IainConnor\PeaceKeeper\Drivers;


use GuzzleHttp\Client;
use IainConnor\GameMaker\ControllerInformation;
use IainConnor\GameMaker\Endpoint;
use IainConnor\GameMaker\GameMaker;
use IainConnor\JabberJay\JabberJay;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AcceptanceTest extends Driver
{
    /**
     * Drive a request for the given inputs to the given endpoint by performing an actual HTTP request.
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
        $symfonyRequest = $jabberJay->getMockRequestForEndpoint($endpoint, $inputs);

        $client = new Client();
        $requestParams = [];

        foreach ( $endpoint->inputs as $input ) {
            switch ($input->in) {
                case "QUERY":
                    $requestParams['query'][$input->name] = $symfonyRequest->query->get($input->name);

                    break;
                case "FORM":
                    $requestParams['form_params'][$input->name] = $symfonyRequest->request->get($input->name);

                    break;
                case "BODY":
                    $requestParams['body'] = $symfonyRequest->getContent();

                    break;
                case "HEADER":
                    $requestParams['headers'][$input->name] = $symfonyRequest->headers->get($input->name);

                    break;
            }
        }

        $guzzleResponse = $client->request(GameMaker::getAfterLastSlash(get_class($endpoint->httpMethod)), $symfonyRequest->getUri(), $requestParams);

        return new JsonResponse($guzzleResponse->getBody()->getContents(), $guzzleResponse->getStatusCode(), $guzzleResponse->getHeaders());
    }

}