<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

class TransactionOrder
{
    #[Serializer\Type("uuid")]
    public UuidInterface $uuid;

    public int $processedQuantity;

    public string $averagePrice;

    public Instrument $instrument;
}
