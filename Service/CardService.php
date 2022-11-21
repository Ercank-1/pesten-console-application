<?php
require_once('../Model/Player.php');
require_once('../Model/Card.php');

class CardService
{
    public array $players = ['Alice', 'Bob', 'Carol', 'Eve'];
    public array $playerModels = [];
    public array $cards = [];

    public function init():array
    {
        $cards = $this->generateCards();
        $players = $this->generatePlayers();
        $players = $this->setCards($players, $cards);
        $this->playerModels = $players;
        return $players;
    }

    public function generateCards():array
    {
        $start = 2;
        $end = 14;

        $cardsModels = [];
        foreach (Card::CARD_TYPES as $cardType) {
            for ($number = $start; $number <= $end; $number++) {
                $cardModel = new Card($number, $cardType);
                $cardsModels[] = $cardModel;
            }
        }

        return $cardsModels;
    }

    /** @return Player[] */
    public function generatePlayers():array
    {
        $players = [];
        foreach ($this->players as $playerName) {
            $playerModel = new Player();
            $playerModel->setName($playerName);
            $players[] = $playerModel; 
        }

        return $players;
    }

    public function setCards(array $players, array $cards):array
    {
        /** @var Player $player */
        foreach ($players as $key => $player) {
            $randomCards = array_rand($cards, 7);
            foreach ($randomCards as $randomId) {
                $player->setCard($cards[$randomId]);
                $players[$key] = $player;
                unset($cards[$randomId]);
            }
        }
        
        $this->cards = $cards;
        return $players;
    }

    public function getAvailableCards():array
    {
        return $this->cards;
    }

    /** @return Player[] */
    public function getPlayers():array
    {
        return $this->playerModels;
    }
}