<?php
require_once('../Model/Player.php');
require_once('../Model/Card.php');
require_once('../Model/Result.php');
require_once('../Model/GameResultCase.php');

class CardService
{
    private array $players = [];
    private array $playerModels = [];
    private array $cards = [];
    private array $availableCards = [];
    private Card $topCard;
    private int $countNotPlayed = 0;

    public function init(array $players):array
    {
        $this->players = $players;

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

    public function playCard(Player $player, int $key)
    {
        $card = $player->getCard($key);
        $this->topCard = $card;
        $player->setPlayedCard($card);
        $player->removeCard($key);
    }

    public function getTopCard():Card
    {
        return $this->topCard;
    }

    public function playGame(Player $player):Result
    {
        $hasTypeCardKey = $player->hasTypeCard($this->getTopCard());
        $hasSameValueCard = $player->hasSameValueCard($this->getTopCard());

        if (isset($hasTypeCardKey)) {
            $this->countNotPlayed = 0;
            $this->playCard($player, $hasTypeCardKey);
            return new Result(GameResultCase::Played, $player);
        } else if (isset($hasSameValueCard)) {
            $this->countNotPlayed = 0;
            $this->playCard($player, $hasSameValueCard);
            return new Result(GameResultCase::TopCardTypeChanged, $player);
        } else if (!empty($this->getAvailableCards())) {
            $this->playerAddCard($player);
            return new Result(GameResultCase::TakesCard, $player);
        }

        if (!$player->hasCards()) {
            return new Result(GameResultCase::Winner, $player);
        }

        if ($this->countNotPlayed == count($this->players)) {
            return new Result(GameResultCase::NobodyCanPlay, $player);
        } else {
            $this->countNotPlayed++;
        }

        return new Result(GameResultCase::NotPLAYED, $player);
    }
}