<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class Venue
{
    public string $name;

    public string $title;

    public string $mic;

    public bool $isOpen;

    public OpeningHours $openingHours;

    /**
     * @var string[]
     */
    #[Serializer\Type("array<string>")]
    public array $openingDays;
}
