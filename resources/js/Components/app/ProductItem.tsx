import React from "react";
import { Product } from "@/types";
import { Link, useForm } from "@inertiajs/react";
import CurrencyFormatter from "@/Components/core/CurrencyFormatter";

function ProductItem({ product }: { product: Product }) {
    const form = useForm<{
        option_ids: Record<string, number>;
        quantity: number;
        price: number | null;
    }>({
        option_ids: {},
        quantity: 1,
        price: 0,
    });
    const handleCart = () => {
        form.post(route("cart.store", product.id), {
            preserveScroll: true,
            preserveState: true,
            onError: (er) => {
                console.log(er);
            },
        });
    };

    return (
        <div className={"card bg-base-100 shadow-xl p-3"}>
            <Link href={route("product.show", product.slug)}>
                <figure>
                    <img
                        src={product.image}
                        className={"aspect-square object-cover"}
                        alt={product.title}
                    />
                </figure>
            </Link>
            <div className="card-body">
                <h2 className="card-title">{product.title}</h2>
                <p>
                    by{" "}
                    <Link href={""} className={"hover:underline"}>
                        {product.user.name}
                    </Link>{" "}
                    &nbsp; in{" "}
                    <Link href={""} className={"hover:underline"}>
                        {product.department.name}
                    </Link>
                </p>
            </div>
            <div className="card-actions items-center justify-between">
                <button onClick={handleCart} className="btn btn-primary">
                    Add to Cart
                </button>
                <span className="text-xl">
                    <CurrencyFormatter amount={product.price} />
                </span>
            </div>
        </div>
    );
}

export default ProductItem;
