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

        $players = ['Alice', 'Bob', 'Carol', 'Eve'];
        $players = $this->cardService->init($players);

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
                $result = $this->cardService->playGame($player);
                switch ($result->getGameResult()) {
                    case GameResultCase::Played:
                        $this->printPlayedCard($player);
                        break;
                    case GameResultCase::TakesCard:
                        $this->printPlayerTakesNewCard($player);
                        break;
                    case GameResultCase::TopCardTypeChanged:
                        $this->printPlayedCard($player);
                        $this->printTopCardChanged($player);
                        break;
                    case GameResultCase::NotPLAYED:
                        $count++;
                        break;
                    case GameResultCase::Winner:
                        $this->playerWin($player);
                        exit;
                    case GameResultCase::NobodyCanPlay:
                        $this->printService->printText('Nobody can play anymore!');
                        $this->printService->printEnter();
                        $this->showPlayerCards($this->cardService->getPlayers());
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


    public function printTopCardChanged(Player $player)
    {
        $text =  sprintf('Top card type changed to %s%s', $player->getPlayedCard()->getValue(), $player->getPlayedCard()->getIcon());
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

