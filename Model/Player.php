<?php

class Player
{
    private string $name;
    private array $cards = [];

    public function setCard(Card $card) {
        $this->cards[] = $card;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCards():array
    {
        return $this->cards;
    }

    public function getCard(int $key)
    {
        return $this->cards[$key];
    }

    public function hasTypeCard(Card $cardModel)
    {
        foreach ($this->cards as $key => $card) {
            if ($cardModel->getType() == $card->getType()) {
                return $key;
            }
        }
    }

    public function removeCard(int $key)
    {
        unset($this->cards[$key]);
    }

    public function addCard(Card $card)
    {
        $this->cards[] = $card;
    }

    public function hasCards()
    {
        if (!empty($this->cards)) {
            return true;
        }
    }

    public function hasSameValueCard($cardModel)
    {
        foreach ($this->cards as $key => $card) {
            if ($cardModel->getValue() == $card->getValue()) {
                return $key;
            }
        }
    }

}