<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Instruments extends Pageable
{
    /**
     * @var InstrumentDetails[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\InstrumentDetails>")]
    public array $results;
}
