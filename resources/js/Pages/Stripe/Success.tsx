import React from "react";
import { CheckCircleIcon } from "@heroicons/react/24/outline";
import { Order, PageProps } from "@/types";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import CurrencyFormatter from "@/Components/core/CurrencyFormatter";

export default function Success({ orders }: PageProps<{ orders: Order[] }>) {
    console.log(orders);
    return (
        <AuthenticatedLayout>
            <Head title="Payment Was Completed" />
            <div className="mx-auto py-8 px-4 w-[480px]">
                <div className="flex flex-col gap-2 items-center">
                    <div className="text-6xl text-center text-emerald-600">
                        <CheckCircleIcon className="size-24" />
                    </div>
                    <div className="text-3xl">Payment Was Completed</div>
                </div>
                <div className="my-6 text-lg">
                    Thank you for your purhase. Your payment was completed
                    successfully.
                </div>
                {orders.map((order) => (
                    <div
                        key={order.id}
                        className=" bg-white dark:bg-gray-800 rounded-lg p-6 mb-4"
                    >
                        <h3 className="text-3xl mb-3">Order Summary</h3>
                        <div className="flex justify-between mb-2 font-bold">
                            <div className="text-gray-600">Seller</div>
                            <Link href="#" className="hover:underline">
                                {order.vendorUser.store_name}
                            </Link>
                        </div>
                        <div className="flex justify-between mb-4">
                            <div className="text-gray-400">Order Number</div>
                            <div>
                                <Link href="#">#{order.id}</Link>
                            </div>
                        </div>
                        <div className="flex justify-between mb-2">
                            <div className="text-gray-400">Items</div>
                            <div>{order.orderItems.length}</div>
                        </div>
                        <div className="flex justify-between mb-3">
                            <div className="text-gray-400">Total</div>
                            <div>
                                <CurrencyFormatter amount={order.total_price} />
                            </div>
                        </div>
                        <div className="flex justify-between mt-4">
                            <Link href="#" className="btn btn-primary">
                                View Order Detail
                            </Link>
                            <Link href={route("dashboard")} className="btn">
                                Back To Home
                            </Link>
                        </div>
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
