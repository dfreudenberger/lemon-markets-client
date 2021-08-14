<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Orders extends Pageable
{
    /**
     * @var Order[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\Order>")]
    public array $results;
}
