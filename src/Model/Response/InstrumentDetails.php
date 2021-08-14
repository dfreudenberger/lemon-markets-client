<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation\Type;

class InstrumentDetails extends Instrument
{
    public string $wkn;

    public string $name;

    public string $symbol;

    public string $type;

    /**
     * @var InstrumentVenue[]
     */
    #[Type("array<LemonMarketsClient\Model\Response\InstrumentVenue>")]
    public array $venues;
}
