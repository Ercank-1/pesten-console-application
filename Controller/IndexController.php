<?php
require_once('../Service/CardService.php');
require_once('../Service/PrintService.php');

class IndexController
{
    private array $availableCards = [];
    private Card $topCard;
    private CardService $cardService;
    private PrintService $printService;

    function __construct() {
        $this->cardService = new CardService();
        $this->printService = new PrintService();

        $players = $this->cardService->init();
        $this->availableCards  = $this->cardService->getAvailableCards();

        $this->setTopCard();
        $this->showPlayerCards($players);
        $this->startGame($players);
    }

    public function showPlayerCards(array $players)
    {
        /** @var Player $player */
        /** @var Card $card */
        foreach ($players as $player) {
            $playerText = sprintf("user %s: ",$player->getName());
            $cardValue = '';
            foreach ($player->getCards() as $card) {
                $cardValue .= sprintf('%s%s ',$card->getValue(), $card->getIcon());
            }

            $playerCardsText =  $playerText . $cardValue;
            $this->printService->printText($playerCardsText);
            $this->printService->printEnter();
        }

        $text = sprintf('Top card is: %s%s', $this->topCard->getValue(), $this->topCard->getIcon());
        $this->printService->printText($text);
        $this->printService->printEnter();

    }

    public function printCard(Card $card, string $text = '')
    {
        $text = sprintf("%s %s%s ", $text, $card->getValue(), $card->getIcon());
        $this->printService->printText($text);
        $this->printService->printEnter();
    }

    public function startGame(array $players)
    {
        /** @var Player $player */
        /** @var Card $card */
        while (true) {
            $count = 0;
            foreach ($players as $player) {
                $hasTypeCard = $player->hasTypeCard($this->topCard);
                $hasSameValueCard = $player->hasSameValueCard($this->topCard);

                if (isset($hasTypeCard)) {
                    $this->playCard($player, $hasTypeCard);
                } else if (isset($hasSameValueCard)) {
                    $card = $player->getCard($hasSameValueCard);
                    $this->playCard($player, $hasSameValueCard);
                    $this->printTopCardChanged($card);
                } else if (!empty($this->availableCards)) {
                    $this->playerAddCard($player);
                } else {
                    $count++;
                }

                if (!$player->hasCards()) {
                    $this->playerWin($player);
                    exit;
                }

                if ($count == count($this->cardService->getPlayers())) {
                    $this->printService->printText('Nobody can play anymore!');
                    exit;
                }
            }
        }

    }

    public function setTopCard()
    {
        $randId = array_rand($this->availableCards);
        $startCard = $this->availableCards[$randId];
        $this->topCard = $startCard;
        $this->removeAvailableCard($randId);
    }

    public function printPlayedCard(Player $player, $key)
    {
        $text = sprintf("%s plays", $player->getName());
        $this->printCard($player->getCard($key), $text);
        $player->removeCard($key);
    }

    public function playerAddCard(Player $player)
    {
        $randomId = array_rand($this->availableCards);
        $player->addCard($this->availableCards[$randomId]);
        $text = sprintf('Player %s takes new card..', $player->getName());
        $this->printCard($this->availableCards[$randomId], $text);
        $this->removeAvailableCard($randomId);
    }


    public function printTopCardChanged(Card $card)
    {
        $text =  sprintf('Top card type changed to %s%s', $card->getValue(), $card->getIcon());
        $this->printService->printText($text);
        $this->printService->printEnter();
    }

    public function playerWin(Player $player)
    {
        $this->printService->printText('DONE!');
        $this->printService->printEnter();
        $this->showPlayerCards($this->cardService->getPlayers());
        $text =  sprintf('Player %s win the game!', $player->getName());
        $this->printService->printText($text);
    }

    public function removeAvailableCard(int $id)
    {
        unset($this->availableCards[$id]);
    }

    public function playCard(Player $player, int $key)
    {
        $card = $player->getCard($key);
        $this->topCard = $card;
        $this->printPlayedCard($player, $key);
    }

}

new IndexController();

