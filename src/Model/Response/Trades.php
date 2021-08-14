<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Trades extends Pageable
{
    /**
     * @var Trade[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\Trade>")]
    public array $results;
}
