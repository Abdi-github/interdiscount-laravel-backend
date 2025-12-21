import type { AdminType, OrderStatus, PaymentStatus, PaymentMethod, ProductStatus, AvailabilityState, TransferStatus, DiscountType } from './enums';

export interface TranslatedField {
    de: string;
    en: string;
    fr: string;
    it: string;
}

export interface Admin {
    id: number;
    email: string;
    first_name: string;
    last_name: string;
    phone: string | null;
    admin_type: AdminType;
    store_id: number | null;
    avatar_url: string | null;
    is_active: boolean;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface User {
    id: number;
    email: string;
    first_name: string;
    last_name: string;
    phone: string | null;
    preferred_language: string;
    avatar_url: string | null;
    is_active: boolean;
    is_verified: boolean;
    verified_at: string | null;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    name: string;
    name_short: string | null;
    slug: string;
    code: string;
    displayed_code: string | null;
    brand_id: number;
    category_id: number;
    price: number;
    original_price: number | null;
    currency: string;
    images: ProductImage[];
    rating: number;
    review_count: number;
    specification: string | null;
    availability_state: AvailabilityState;
    delivery_days: number | null;
    in_store_possible: boolean;
    release_date: string | null;
    services: ProductService[];
    promo_labels: string[];
    is_speed_product: boolean;
    is_orderable: boolean;
    is_sustainable: boolean;
    is_active: boolean;
    status: ProductStatus;
    brand?: Brand;
    category?: Category;
    created_at: string;
    updated_at: string;
}

export interface ProductImage {
    alt: string;
    src: {
        xs?: string;
        sm?: string;
        md?: string;
    };
}

export interface ProductService {
    code: string;
    name: string;
    price: number;
}

export interface Category {
    id: number;
    name: TranslatedField;
    slug: string;
    category_id: string;
    level: number;
    parent_id: number | null;
    sort_order: number;
    is_active: boolean;
    children?: Category[];
    created_at: string;
    updated_at: string;
}

export interface Brand {
    id: number;
    name: string;
    slug: string;
    product_count: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface Store {
    id: number;
    name: string;
    slug: string;
    store_id: string;
    street: string;
    street_number: string;
    postal_code: string;
    city_id: number;
    canton_id: number;
    remarks: string | null;
    phone: string | null;
    email: string | null;
    latitude: number;
    longitude: number;
    format: string | null;
    is_xxl: boolean;
    opening_hours: OpeningHours[];
    is_active: boolean;
    city?: City;
    canton?: Canton;
    created_at: string;
    updated_at: string;
}

export interface OpeningHours {
    day: TranslatedField;
    open: string;
    close: string;
    is_closed: boolean;
}

export interface Order {
    id: number;
    order_number: string;
    user_id: number;
    shipping_address_id: number | null;
    billing_address_id: number | null;
    status: OrderStatus;
    payment_method: PaymentMethod;
    payment_status: PaymentStatus;
    subtotal: number;
    shipping_fee: number;
    discount: number;
    total: number;
    currency: string;
    coupon_code: string | null;
    notes: string | null;
    store_pickup_id: number | null;
    is_store_pickup: boolean;
    estimated_delivery: string | null;
    delivered_at: string | null;
    cancelled_at: string | null;
    cancellation_reason: string | null;
    user?: User;
    items?: OrderItem[];
    created_at: string;
    updated_at: string;
}

export interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    product_name: string;
    product_code: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    currency: string;
    product?: Product;
}

export interface Review {
    id: number;
    product_id: number;
    user_id: number;
    rating: number;
    title: string | null;
    comment: string | null;
    language: string;
    is_verified_purchase: boolean;
    is_approved: boolean;
    helpful_count: number;
    product?: Product;
    user?: User;
    created_at: string;
    updated_at: string;
}

export interface Coupon {
    id: number;
    code: string;
    description: TranslatedField;
    discount_type: DiscountType;
    discount_value: number;
    minimum_order: number;
    max_uses: number;
    used_count: number;
    valid_from: string;
    valid_until: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface StoreInventory {
    id: number;
    store_id: number;
    product_id: number;
    quantity: number;
    reserved: number;
    min_stock: number;
    max_stock: number;
    last_restock_at: string | null;
    last_sold_at: string | null;
    location_in_store: string | null;
    is_display_unit: boolean;
    is_active: boolean;
    product?: Product;
    store?: Store;
    created_at: string;
    updated_at: string;
}

export interface StockTransfer {
    id: number;
    transfer_number: string;
    from_store_id: number;
    to_store_id: number;
    initiated_by: number;
    status: TransferStatus;
    items: TransferItem[];
    notes: string | null;
    approved_by: number | null;
    shipped_at: string | null;
    received_at: string | null;
    from_store?: Store;
    to_store?: Store;
    created_at: string;
    updated_at: string;
}

export interface TransferItem {
    product_id: number;
    product_name: string;
    quantity: number;
    received_quantity: number;
}

export interface StorePromotion {
    id: number;
    store_id: number;
    product_id: number | null;
    category_id: number | null;
    title: string;
    description: string | null;
    discount_type: DiscountType;
    discount_value: number;
    buy_quantity: number | null;
    get_quantity: number | null;
    valid_from: string;
    valid_until: string;
    is_active: boolean;
    created_by: number;
    store?: Store;
    created_at: string;
    updated_at: string;
}

export interface Canton {
    id: number;
    name: TranslatedField;
    code: string;
    slug: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface City {
    id: number;
    name: TranslatedField;
    canton_id: number;
    postal_codes: string[];
    slug: string;
    is_active: boolean;
    canton?: Canton;
    created_at: string;
    updated_at: string;
}

export interface Role {
    id: number;
    name: string;
    display_name: TranslatedField;
    description: TranslatedField;
    is_system: boolean;
    is_active: boolean;
    permissions?: Permission[];
    created_at: string;
    updated_at: string;
}

export interface Permission {
    id: number;
    name: string;
    display_name: TranslatedField;
    description: TranslatedField;
    resource: string;
    action: string;
    is_active: boolean;
}

export interface Address {
    id: number;
    user_id: number;
    label: string | null;
    first_name: string;
    last_name: string;
    street: string;
    street_number: string;
    postal_code: string;
    city: string;
    canton_code: string;
    country: string;
    phone: string | null;
    is_default: boolean;
    is_billing: boolean;
}

export interface Notification {
    id: number;
    user_id: number;
    type: string;
    title: string;
    message: string;
    data: Record<string, unknown> | null;
    is_read: boolean;
    read_at: string | null;
    created_at: string;
}

export interface Pagination {
    page: number;
    limit: number;
    total: number;
    totalPages: number;
}

export interface PaginatedResponse<T> {
    success: boolean;
    message: string;
    data: T[];
    pagination: Pagination;
}
