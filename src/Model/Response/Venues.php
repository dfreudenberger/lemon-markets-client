<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Venues
{
    /**
     * @var Venue[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\Venue>")]
    public array $results;
}
