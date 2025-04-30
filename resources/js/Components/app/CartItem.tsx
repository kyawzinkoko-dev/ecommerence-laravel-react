import { productRoute } from "@/helper";
import { CartItems } from "@/types";
import { Link, router, useForm, usePage } from "@inertiajs/react";
import { it } from "node:test";
import React, { useState } from "react";
import TextInput from "../core/TextInput";
import CurrencyFormatter from "../core/CurrencyFormatter";

export default function CartItem({ item }: { item: CartItems }) {
    const [error, setError] = useState<string>("");
    const deleteForm = useForm({
        option_ids: item.option_ids,
    });

    const onDeleteClick = () => {
        deleteForm.delete(route("cart.destroy", item.product_id), {
            preserveScroll: true,
        });
    };

    const handleQuantityChange = (ev: React.ChangeEvent<HTMLInputElement>) => {
        setError("");

        router.put(
            route("cart.update", item.product_id),
            {
                quantity: ev.target.value,
                option_ids: item.option_ids,
            },
            {
                preserveScroll: true,
                onError: (e) => {
                    setError(Object.values(e)[0]);
                },
            }
        );
    };

    return (
        <>
            <div key={item.id} className="flex gap-6 p-3">
                <Link
                    href={productRoute(item)}
                    className="w-32 min-w-32 min-h-32 flex justify-center self-start"
                >
                    <img src={item.image} className="max-w-full max-h-full" />
                </Link>
                <div className="flex-1 flex flex-col">
                    <div className="flex-1">
                        <h3 className="mb-3 text-sm font-semibold">
                            <Link href={productRoute(item)}>{item.title}</Link>
                        </h3>
                        <div className="text-xs">
                            {item.options?.map((op) => (
                                <div key={op.id}>
                                    <strong className="text-bold">
                                        {op.type.name}
                                    </strong>
                                    {op.name}
                                </div>
                            ))}
                        </div>
                    </div>
                    <div className="flex justify-between items-center mt-4">
                        <div className="flex gap-2 items-center">
                            <div className="text-sm">Quantity: </div>
                            <div
                                className={
                                    error
                                        ? " tooltip tooltip-open tooltip-error"
                                        : ""
                                }
                                data-tip={error}
                            >
                                <TextInput
                                    type="number"
                                    defaultValue={item.quantity}
                                    onChange={handleQuantityChange}
                                    className="input-sm w-16"
                                />
                            </div>

                            <button
                                onClick={() => onDeleteClick()}
                                className="btn btn-sm btn-ghost"
                            >
                                Delete
                            </button>
                            <button className="btn btn-sm btn-ghost">
                                Save For Later
                            </button>
                        </div>
                        <div className="font-bold text-lg">
                            <CurrencyFormatter
                                amount={item.price * item.quantity}
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div className="divider"></div>
        </>
    );
}
