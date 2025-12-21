<?php

use App\Domain\Order\Enums\OrderStatus;

test('order status has correct terminal states', function () {
    $terminalStates = [
        OrderStatus::CANCELLED,
        OrderStatus::RETURNED,
        OrderStatus::PICKED_UP,
        OrderStatus::PICKUP_EXPIRED,
    ];

    foreach ($terminalStates as $status) {
        expect($status)->toBeInstanceOf(OrderStatus::class);
    }
});

test('order status PENDING can transition to CONFIRMED or CANCELLED', function () {
    $validTransitions = OrderStatus::PENDING->validTransitions();

    expect($validTransitions)->toContain(OrderStatus::CONFIRMED)
        ->toContain(OrderStatus::CANCELLED);
});

test('order status CONFIRMED can transition to PROCESSING or CANCELLED', function () {
    $validTransitions = OrderStatus::CONFIRMED->validTransitions();

    expect($validTransitions)->toContain(OrderStatus::PROCESSING)
        ->toContain(OrderStatus::CANCELLED);
});

test('order status SHIPPED can only transition to DELIVERED', function () {
    $validTransitions = OrderStatus::SHIPPED->validTransitions();

    expect($validTransitions)->toContain(OrderStatus::DELIVERED)
        ->not->toContain(OrderStatus::CANCELLED);
});

test('terminal states have no valid transitions', function () {
    expect(OrderStatus::CANCELLED->validTransitions())->toBeEmpty();
    expect(OrderStatus::RETURNED->validTransitions())->toBeEmpty();
    expect(OrderStatus::PICKED_UP->validTransitions())->toBeEmpty();
});

test('canTransitionTo validates correctly', function () {
    expect(OrderStatus::PENDING->canTransitionTo(OrderStatus::CONFIRMED))->toBeTrue();
    expect(OrderStatus::PENDING->canTransitionTo(OrderStatus::SHIPPED))->toBeFalse();
    expect(OrderStatus::DELIVERED->canTransitionTo(OrderStatus::RETURNED))->toBeTrue();
    expect(OrderStatus::DELIVERED->canTransitionTo(OrderStatus::CANCELLED))->toBeFalse();
});
