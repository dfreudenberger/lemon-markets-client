<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

class Space
{
    #[Serializer\Type("uuid")]
    public UuidInterface $uuid;

    public string $name;

    public SpaceState $state;

    public string $type;
}
