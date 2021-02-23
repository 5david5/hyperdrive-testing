<?php

declare(strict_types=1);

namespace Hyperdrive\Panels\Options;

use JetBrains\PhpStorm\ArrayShape;

class HyperspaceJumpOptions
{
    #[ArrayShape([
        "short" => "string",
        "long" => "string",
        "quit" => "string"
    ])]
    public function __invoke(): array
    {
        return [
            "short" => "Short jump - 10 planets",
            "long" => "Long jump - 20 planets",
            "quit" => "Quit",
        ];
    }
}