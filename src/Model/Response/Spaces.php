<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Spaces extends Pageable
{
    /**
     * @var Space[]
     */
    #[Serializer\Type("array<LemonMarketsClient\Model\Response\Space>")]
    public array $results;
}
