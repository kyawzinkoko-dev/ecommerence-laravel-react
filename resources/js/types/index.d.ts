import {Config} from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    vendor: {
        status: string,
        status_label:string,
        store_name: string,
        store_address: string,
        cover_image:string
    },
    stripe_account_active: boolean
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
        ziggy: Config & { location: string };
        success: {
            message: string,
            time:number
    }
    totalPrice: number,
    totalQuantity: number,
    miniCartItems: CartItems[],
    csrf_token: string
};
export type CartItems = {
    id: number,
    product_id: number,
    title: string,
    slug: string,
    price: number,
    quantity: number,
    image: string,
    option_ids: Record<string, number>,
    options: VariationTypeOption[],
}
export type GroupedCartItems ={
    user:User,
    items:CartItems[],
    totalPrice:number,
    totalQuantity:number,
}
export type Product = {
    id: number,
    title: string,
    slug: string,
    price: number,
    quantity: number,
    image: string,
    images: Image[],
    description: string,
    short_description: string,
    user: {
        id: number,
        name: string
    },
    department: {
        id: number,
        name: string
    },
    variationTypes: VariationType[],
    variations: Array<{
        id: number,
        variations_type_option_ids: number [],
        quantity: number,
        price: number

    }>
}

export type VariationType = {
    id: number
    name: string,
    type: 'Select' | 'Radio' | 'Image',
    options: VariationTypeOption[]
}

export type VariationTypeOption = {
    id: number,
    name: string,
    images: Image[],
    type: VariationType
}


export type PaginationTypeProps<T> = {
    data: Array<T>
}

export type Image = {
    id: number,
    thumb: string,
    small: string,
    medium: string,
    large: string
}

export type OrderItem = {
    id: number,
    quantity: number,
    price: number,
    variation_type_option_ids: number[],
    product: {
        id: number,
        title: string,
        slug: string,
        description: string,
        image:string,
    }
}
export type Order = {
    id: number,
    total_price: number,
    status: string,
    created_at: string,
    vendorUser: {
        id: string,
        name: string,
        email: string,
        store_name: string,
        store_address: string,
    }
    orderItems:OrderItem[]
}

