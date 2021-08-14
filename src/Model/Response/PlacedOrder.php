<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

class PlacedOrder
{
    public string $isin;

    public int $validUntil;

    public string $side;

    public int $quantity;

    public ?string $stopPrice;

    public ?string $limitPrice;

    #[Serializer\Type("uuid")]
    public UuidInterface $uuid;

    public string $status;

    public string $tradingVenueMic;
}
