<?php

class Result
{
    private GameResultCase $result;
    private Player $player;

    public function __construct(GameResultCase $result, Player $player)
    {
        $this->result = $result;
        $this->player = $player;
    }

    public function getGameResult():GameResultCase
    {
        return $this->result;
    }

    public function getPlayer():Player
    {
        return $this->player;
    }

}