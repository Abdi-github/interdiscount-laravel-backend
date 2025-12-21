export enum AdminType {
    SUPER_ADMIN = 'super_admin',
    PLATFORM_ADMIN = 'platform_admin',
    STORE_MANAGER = 'store_manager',
    STORE_STAFF = 'store_staff',
    SUPPORT_AGENT = 'support_agent',
}

export enum OrderStatus {
    PENDING = 'pending',
    CONFIRMED = 'confirmed',
    PROCESSING = 'processing',
    SHIPPED = 'shipped',
    DELIVERED = 'delivered',
    READY_FOR_PICKUP = 'ready_for_pickup',
    PICKED_UP = 'picked_up',
    CANCELLED = 'cancelled',
    RETURNED = 'returned',
    PICKUP_EXPIRED = 'pickup_expired',
}

export enum PaymentStatus {
    PENDING = 'pending',
    PROCESSING = 'processing',
    SUCCEEDED = 'succeeded',
    FAILED = 'failed',
    REFUNDED = 'refunded',
    PARTIALLY_REFUNDED = 'partially_refunded',
}

export enum PaymentMethod {
    CARD = 'card',
    TWINT = 'twint',
    POSTFINANCE = 'postfinance',
    INVOICE = 'invoice',
}

export enum ProductStatus {
    PUBLISHED = 'published',
    DRAFT = 'draft',
    INACTIVE = 'inactive',
    ARCHIVED = 'archived',
}

export enum AvailabilityState {
    INSTOCK = 'INSTOCK',
    ONORDER = 'ONORDER',
    RESERVATION = 'RESERVATION',
    OUT_OF_STOCK = 'OUT_OF_STOCK',
    DISCONTINUED = 'DISCONTINUED',
}

export enum TransferStatus {
    REQUESTED = 'requested',
    APPROVED = 'approved',
    REJECTED = 'rejected',
    SHIPPED = 'shipped',
    RECEIVED = 'received',
    CANCELLED = 'cancelled',
}

export enum DiscountType {
    PERCENTAGE = 'percentage',
    FIXED = 'fixed',
    BUY_X_GET_Y = 'buy_x_get_y',
}
