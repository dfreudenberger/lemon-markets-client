<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Quotes extends Pageable
{
    /**
     * @var Quote[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\Quote>")]
    public array $results;
}
