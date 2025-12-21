<?php

namespace App\Domain\Review\Listeners;

use App\Domain\Notification\Models\Notification;
use App\Domain\Review\Events\ReviewCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyReviewModeration implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(ReviewCreated $event): void
    {
        $review = $event->review;
        $review->loadMissing(['product', 'user']);

        Notification::create([
            'user_id' => $review->user_id,
            'type' => 'review_submitted',
            'title' => 'Review submitted',
            'message' => "Your review for {$review->product->name} has been submitted and is pending moderation.",
            'data' => [
                'review_id' => $review->id,
                'product_id' => $review->product_id,
                'product_name' => $review->product->name ?? '',
            ],
            'is_read' => false,
        ]);
    }
}
