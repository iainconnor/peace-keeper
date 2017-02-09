<?php


namespace IainConnor\PeaceKeeper\Drivers;


use IainConnor\GameMaker\Endpoint;
use IainConnor\GameMaker\GameMaker;

abstract class Driver
{
    /** @var GameMaker */
    protected $gameMaker;

    /**
     * Driver constructor.
     * @param GameMaker $gameMaker
     */
    public function __construct(GameMaker $gameMaker)
    {
        $this->gameMaker = $gameMaker;
    }

    public abstract function driveRequest(Endpoint $endpoint, array $inputs = []);
}