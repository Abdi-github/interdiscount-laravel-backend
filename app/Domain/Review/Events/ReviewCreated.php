<?php

namespace App\Domain\Review\Events;

use App\Domain\Review\Models\Review;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Review $review) {}
}
