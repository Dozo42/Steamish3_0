<?php

namespace App\Twig;

use App\Service\TimeConverterService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeConverterExtension extends AbstractExtension
{

    public function __construct(
        private TimeConverterService $timeConverterService
    ) { }

    public function getFilters(): array
    {
        return [
            new TwigFilter('time_to_string', [$this, 'timeToString']),
        ];
    }

    public function timeToString(int $gameTime): string
    {
        return $this->timeConverterService->convertTimeToString($gameTime);
    }
}