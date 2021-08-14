<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Portfolio extends Pageable
{
    /**
     * @var PortfolioRecord[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\PortfolioRecord>")]
    public array $results;
}
