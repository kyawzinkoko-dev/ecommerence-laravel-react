import React from "react";
import CurrencyFormatter from "@/Components/core/CurrencyFormatter";
import { Link, usePage } from "@inertiajs/react";
import { productRoute } from "@/helper";

function MiniCartDropdown() {
    const { totalQuantity, totalPrice, miniCartItems } = usePage().props;
    console.log(usePage().props)
    return (
        <div className="dropdown dropdown-end">
            <div
                tabIndex={0}
                role="button"
                className="btn btn-ghost btn-circle"
            >
                <div className="indicator">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                    <span className="badge badge-sm indicator-item">
                        {totalQuantity}
                    </span>
                </div>
            </div>
            <div
                tabIndex={0}
                className="card card-compact dropdown-content bg-base-100 z-[1] mt-3 w-[480px] shadow"
            >
                <div className="card-body">
                    <span className="text-lg font-bold">
                        {totalQuantity} Items
                    </span>
                    <div className="my-4 max-h-[300px] overflow-auto">
                        {miniCartItems.length === 0 && (
                            <div className="py-2 text-gray-500 text-center">
                                You don't have nay items yet.
                            </div>
                        )}
                                   
                     
                        {miniCartItems.map((items) => (
                            <div key={items.id} className="flex gap-4 p-3">
                                <Link
                                    className="w-16 h-16"
                                    href={productRoute(items)}
                                >
                                    <img
                                        src={items.image}
                                        alt={items.title}
                                        className="max-w-full max-h-full"
                                    />
                                </Link>
                                <div className="flex-1">
                                    <h3 className="font-semibold mb-3">
                                        <Link
                                            href={route(
                                                "product.show",
                                                items.slug
                                            )}
                                        >
                                            {items.title}
                                        </Link>
                                    </h3>
                                    <div className=" text-xs flex justify-between items-center">
                                        <div>Quantity : {items.quantity}</div>
                                        <div>
                                            {items.quantity * items.price} MMK
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                    <span className="text-info">
                        Subtotal: <CurrencyFormatter amount={totalPrice} />
                    </span>
                    <div className="card-actions">
                        <Link
                            href={route("cart.index")}
                            className="btn btn-primary btn-block"
                        >
                            View cart
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default MiniCartDropdown;
