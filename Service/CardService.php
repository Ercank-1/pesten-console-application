<?php
require_once('../Model/Player.php');
require_once('../Model/Card.php');

class CardService
{
    private array $players = ['Alice', 'Bob', 'Carol', 'Eve'];
    private array $playerModels = [];
    private array $cards = [];
    private array $availableCards = [];
    private Card $topCard;

    public function init():array
    {
        $cards = $this->generateCards();
        $players = $this->generatePlayers();
        $players = $this->setCards($players, $cards);
        $this->playerModels = $players;

        $this->availableCards  = $this->cards;
        $this->setTopCard();
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
        return $this->availableCards;
    }

    /** @return Player[] */
    public function getPlayers():array
    {
        return $this->playerModels;
    }

    public function playerAddCard(Player $player)
    {
        $randomId = array_rand($this->availableCards);
        $player->addCard($this->availableCards[$randomId]);
        $this->availableCards[$randomId];
        $this->removeAvailableCard($randomId);
    }

    public function removeAvailableCard(int $id)
    {
        unset($this->availableCards[$id]);
    }

    public function setTopCard()
    {
        $randId = array_rand($this->availableCards);
        $startCard = $this->availableCards[$randId];
        $this->topCard = $startCard;
        $this->removeAvailableCard($randId);
    }

    public function playCard(Player $player, int $key):Card
    {
        $card = $player->getCard($key);
        $this->topCard = $card;
        $player->setPlayedCard($card);
        $player->removeCard($key);
        return $card;
    }

    public function getTopCard():Card
    {
        return $this->topCard;
    }
}