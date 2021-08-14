<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

class InstrumentVenue
{
    public string $name;

    public string $title;

    public string $mic;

    public bool $isOpen;

    public bool $tradable;

    public string $currency;
}
