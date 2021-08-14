<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

class PortfolioRecord
{
    public Instrument $instrument;

    public int $quantity;

    public string $averagePrice;

    public string $latestTotalValue;
}
