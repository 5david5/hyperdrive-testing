<?php

declare(strict_types=1);

namespace Hyperdrive\Panels;

use Hyperdrive\Contracts\PanelContract;
use Hyperdrive\Galaxy\Geography\Planet;
use Hyperdrive\Panels\Options\HyperspaceJumpOptions;
use Hyperdrive\Player\Navigator\HyperspaceJump;
use Hyperdrive\PriceList\PriceList;
use Symfony\Component\Config\Definition\Exception\Exception;

class HyperspaceJumpPanel extends BasePanel implements PanelContract
{
    public function __construct(protected HyperspaceJump $hyperspaceJump)
    {
        parent::__construct();
    }

    public function selectionSection(): void
    {
        $shortDistance = PriceList::getHyperspaceJumpValues("short")["distance"];
        $longDistance = PriceList::getHyperspaceJumpValues("long")["distance"];

        $hyperspaceJumpOptions = new HyperspaceJumpOptions();
        $result = $this->cli->radio("Select option", $hyperspaceJumpOptions($shortDistance, $longDistance))->prompt();

        try {
            $this->checkResult($result);
            $planet = $this->selectPlanet();
            $this->hyperspaceJump->jumpTo($planet);
        } catch (Exception $exception) {
            $this->showException($exception);
        }
    }

    /**
     * @throws Exception
     */
    private function checkResult(string $result): void
    {
        if ($result === "quit") {
            throw new Exception("Hyperspace jump was canceled");
        }
        $this->hyperspaceJump->setDistance($result);
    }

    /**
     * @throws Exception
     */
    private function selectPlanet(): Planet
    {
        $collection = $this->hyperspaceJump->getOptions();

        if ($collection->count() !== 1) {
            return $this->cli->radio("Select planet to hyperspace jump", $collection->toArray())->prompt();
        }

        $planet = $collection->get(0);
        $this->cli->error("Only one planet meets the requirements for a hyperspace jump");
        $result = $this->cli->radio("Do you want to jump to {$planet}", ["Yes", "No"])->prompt();

        if ($result === "No") {
            throw new Exception("Hyperspace jump was canceled");
        }

        return $planet;
    }
}
