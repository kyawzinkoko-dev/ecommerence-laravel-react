import CartItem from "@/Components/app/CartItem";
import CurrencyFormatter from "@/Components/core/CurrencyFormatter";
import PrimaryButton from "@/Components/core/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { GroupedCartItems, PageProps } from "@/types";
import { CreditCardIcon } from "@heroicons/react/24/outline";
import { Head, Link } from "@inertiajs/react";
const Index = ({
    csrf_token,
    cartItems,
    totalPrice,
    totalQuantity,
}: PageProps<{ cartItems: Record<number, GroupedCartItems> }>) => {
    return (
        <AuthenticatedLayout>
            <Head title="Your Cart" />
            <div className="container p-8 mx-auto flex flex-col gap-6 lg:flex-row">
                <div className="flex-1 card bg-white dark:bg-gray-800 order-2 lg:order-1">
                    <div className="card-body">
                        <h2 className="text-lg font-bold">Shopping Cart</h2>
                        <div className="my-4">
                            {Object.keys(cartItems).length === 0 && (
                                <div className="text-gray-500 py-2 text-center ">
                                    You don't have any item yet!
                                </div>
                            )}

                            {Object.values(cartItems).map((cartItem) => (
                                <div key={cartItem.user.id}>
                                    <div className="flex justify-between items-center pb-4 border-b border-gray-300 mb-4">
                                        <Link href="/" className=" underline">
                                            {cartItem.user.name}
                                        </Link>
                                        <div>
                                            <form
                                                action={route("cart.checkout")}
                                                method="post"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="_token"
                                                    value={csrf_token}
                                                />
                                                <input
                                                    type="hidden"
                                                    name="vendor_id"
                                                    value={cartItem.user.id}
                                                />
                                                <button className="btn-sm btn-ghost">
                                                    <CreditCardIcon className=" size-6" />
                                                    Pay Only for this seller
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    {cartItem.items.map((item) => (
                                        <CartItem item={item} />
                                    ))}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
                <div className="card my-4 lg:m-0 bg-white dark:bg-gray-800 lg:min-w-[260px] oder-1 lg:order-2">
                    <div className="card-body">
                        SubTotal : ({totalQuantity} items): &nbsp;
                        <CurrencyFormatter amount={totalPrice} />
                        <form method="post" action={route("cart.checkout")}>
                            <input
                                type="hidden"
                                name="_token"
                                value={csrf_token}
                            />
                            <PrimaryButton>
                                <CreditCardIcon className="size-6" />
                                Procced To Checkout
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};
export default Index;
