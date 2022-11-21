<?php
require_once('../Service/CardService.php');
require_once('../Service/PrintService.php');

class IndexController
{
    private CardService $cardService;
    private PrintService $printService;

    function __construct() {
        $this->cardService = new CardService();
        $this->printService = new PrintService();

        $players = $this->cardService->init();

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

        $text = sprintf('Top card is: %s%s', $this->cardService->getTopCard()->getValue(),
                $this->cardService->getTopCard()->getIcon());
        $this->printService->printText($text);
        $this->printService->printEnter();

    }

    public function printCard(Player $player, string $text = '')
    {
        $card = $player->getPlayedCard();
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
                $hasTypeCardKey = $player->hasTypeCard($this->cardService->getTopCard());
                $hasSameValueCard = $player->hasSameValueCard($this->cardService->getTopCard());

                if (isset($hasTypeCardKey)) {
                    $this->cardService->playCard($player, $hasTypeCardKey);
                    $this->printPlayedCard($player);
                } else if (isset($hasSameValueCard)) {
                    $card = $this->cardService->playCard($player, $hasSameValueCard);
                    $this->printPlayedCard($player);
                    $this->printTopCardChanged($card);
                } else if (!empty($this->cardService->getAvailableCards())) {
                    $this->cardService->playerAddCard($player);
                    $this->printPlayerTakesNewCard($player);
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

    public function printPlayedCard(Player $player)
    {
        $text = sprintf("%s plays", $player->getName());
        $this->printCard($player, $text);
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

    public function printPlayerTakesNewCard(Player $player)
    {
        $text = sprintf('Player %s takes new card..', $player->getName());
        $this->printService->printText($text);
        $this->printService->printEnter();
    }

}

new IndexController();

