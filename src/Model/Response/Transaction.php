<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

class Transaction
{
    public string $amount;

    #[Serializer\Type("uuid")]
    public UuidInterface $uuid;

    public string $createdAt;

    public string $type;

    public ?TransactionOrder $order;
}
