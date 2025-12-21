<?php

namespace App\Domain\Review\Listeners;

use App\Domain\Product\Models\Product;
use App\Domain\Review\Models\Review;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductRating implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(object $event): void
    {
        $review = $event->review;
        $product = Product::find($review->product_id);

        if (!$product) {
            return;
        }

        $stats = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->first();

        $product->update([
            'rating' => round($stats->avg_rating, 1),
            'review_count' => $stats->review_count,
        ]);
    }
}
