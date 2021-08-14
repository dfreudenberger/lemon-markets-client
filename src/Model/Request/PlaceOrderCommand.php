<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Request;

class PlaceOrderCommand
{
    const SIDE_BUY = 'buy';
    const SIDE_SELL = 'sell';

    public function __construct(
        private string  $isin,
        private string  $validUntil,
        private string  $side,
        private int     $quantity,
        private ?string $stopPrice = null,
        private ?string $limitPrice = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'isin' => $this->isin,
            'valid_until' => $this->validUntil,
            'side' => $this->side,
            'quantity' => $this->quantity,
            'stop_price' => $this->stopPrice,
            'limit_price' => $this->limitPrice,
        ];
    }
}
