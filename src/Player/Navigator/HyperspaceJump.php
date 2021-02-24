<?php

declare(strict_types=1);

namespace Hyperdrive\Player\Navigator;

use Hyperdrive\Galaxy\Geography\Planet;
use Hyperdrive\Player\Capital\Capital;
use Hyperdrive\PriceList\PriceList;
use Illuminate\Support\Collection;
use Symfony\Component\Config\Definition\Exception\Exception;

class HyperspaceJump
{
    protected int $price;
    protected int $distance;

    public function __construct(protected HyperdriveNavigator $hyperdriveNavigator, protected Capital $capital)
    {
    }

    public function setDistance(string $name): void
    {
        $hyperspaceJump = PriceList::getHyperspaceJumpValues($name);
        $this->price = $hyperspaceJump["price"];
        $this->distance = $hyperspaceJump["distance"];
        $this->capital->isThereEnoughMoney($this->price);
    }

    public function jumpTo(Planet $planet): void
    {
        $this->capital->spendingMoney($this->price);
        $this->hyperdriveNavigator->hyperspaceJumpTo($planet);
    }

    public function getOptions(): Collection
    {
        $collection = collect();

        $collection->add($this->getDistantPlanet(
            $this->hyperdriveNavigator->getCurrentPlanet()->getId() - $this->distance
        ));
        $collection->add($this->getDistantPlanet(
            $this->hyperdriveNavigator->getCurrentPlanet()->getId() + $this->distance
        ));

        return $collection->filter(function ($value): bool {
            return !is_null($value);
        });
    }

    private function getDistantPlanet(int $id): ?Planet
    {
        try {
            return $this->hyperdriveNavigator->getRoute()->getPlanetById($id);
        } catch (Exception) {
            return null;
        }
    }
}
