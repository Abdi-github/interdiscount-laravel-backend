<?php

namespace App\Domain\Order\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case PROCESSING = 'PROCESSING';
    case SHIPPED = 'SHIPPED';
    case DELIVERED = 'DELIVERED';
    case READY_FOR_PICKUP = 'READY_FOR_PICKUP';
    case PICKED_UP = 'PICKED_UP';
    case CANCELLED = 'CANCELLED';
    case RETURNED = 'RETURNED';
    case PICKUP_EXPIRED = 'PICKUP_EXPIRED';

    public function validTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::READY_FOR_PICKUP, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED],
            self::DELIVERED => [self::RETURNED],
            self::READY_FOR_PICKUP => [self::PICKED_UP, self::PICKUP_EXPIRED],
            self::PICKED_UP, self::CANCELLED, self::RETURNED, self::PICKUP_EXPIRED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->validTransitions());
    }

    public function isTerminal(): bool
    {
        return empty($this->validTransitions());
    }
}
