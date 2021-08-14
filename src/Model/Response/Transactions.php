<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Transactions extends Pageable
{
    /**
     * @var Transaction[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\Transaction>")]
    public array $results;
}
