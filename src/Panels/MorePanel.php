<?php

declare(strict_types=1);

namespace Hyperdrive\Panels;

use Hyperdrive\Contracts\PanelContract;
use Hyperdrive\Panels\Options\MoreOptions;
use Hyperdrive\Player\Player;
use Symfony\Component\Config\Definition\Exception\Exception;

class MorePanel extends BasePanel implements PanelContract
{
    public function __construct(protected Player $player)
    {
        parent::__construct();
    }

    public function selectionSection(): void
    {
        $moreOptions = new MoreOptions();
        $result = $this->cli->radio("Select option", $moreOptions())->prompt();
        $this->checkResult($result);
    }

    private function checkResult(string $result): void
    {
        switch ($result) {
            case "spaceship":
                $this->cli->table([$this->player->getSpaceshipData()]);
                break;
            case "player":
                $this->cli->table([$this->player->getPlayerData()]);
                break;
            case "refueling":
                try {
                    $this->player->refuelingSpaceship();
                } catch (Exception $exception) {
                    $this->showException($exception);
                }
                break;
            case "map":
                try {
                    $mapPanel = new MapPanel($this->player);
                    $mapPanel->showMap();
                } catch (Exception $exception) {
                    $this->showException($exception);
                }
                break;
            case "jump":
                try {
                    $hyperspaceJumpPanel = new HyperspaceJumpPanel($this->player->hyperspaceJump());
                    $hyperspaceJumpPanel->selectionSection();
                } catch (Exception $exception) {
                    $this->showException($exception);
                }
                break;
            case "quit":
                throw new Exception("You left the game :(");
        }
    }
}
