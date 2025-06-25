import InputError from "@/Components/core/InputError";
import InputLabel from "@/Components/core/InputLabel";
import Modal from "@/Components/core/Modal";
import PrimaryButton from "@/Components/core/PrimaryButton";
import SecondaryButton from "@/Components/core/SecondaryButton";
import TextInput from "@/Components/core/TextInput";
import { Input } from "@headlessui/react";
import { useForm, usePage } from "@inertiajs/react";
import React, { FormEvent, useState } from "react";

export default function VendorDetail({
    className = "",
}: {
    className?: string;
}) {
    const [showBecomeVendorConfirmation, setShowBecomeVendorConfirmation] =
        useState<boolean>(false);
    const [successMessage, setSuccessMessage] = useState("");
    const user = usePage().props.auth.user;
    const token = usePage().props.csrf_token;
    const { data, setData, errors, post, processing, recentlySuccessful } =
        useForm({
            store_name: user.vendor?.store_name || user.name.toLowerCase().replace(/\s+/g,'-'),
            store_address: user.vendor?.store_address,
        });
    const onStoreNameChange = (ev: React.ChangeEvent<HTMLInputElement>) => {
        setData(
            "store_name",
            ev.target.value.toLowerCase().replace(/\s+/g, "-")
        );
    };

    const closeModal = () => {
        setShowBecomeVendorConfirmation(false)
    };
    const becomeVendor = (ev: React.FormEvent<Element>) => {
        ev.preventDefault();
        post(route("vendor.store"), {
            onSuccess: () => {
                closeModal();
                setSuccessMessage("You can now create and publish products");
            },
            onError: (e) => {
                console.log(e);
            },
        });
    };

    const updateVendor = (ev: React.FormEvent<Element>) => {
        ev.preventDefault();
        post(route("vendor.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                setSuccessMessage("Your details were updated");
            },
            onError: (e) => {
                console.log(e);
            },
        });
    };

    const connect = (ev:React.FormEvent<Element>) => {
        ev.preventDefault();
        post(route('stripe.connect'), {
            onError: (error) => {
                console.log(error)
            }
        })
    }
    return (
        <section className={className}>
            {recentlySuccessful && (
                <div className=" toast toast-end toast-top">
                    <div className="alert alert-success">{successMessage}</div>
                </div>
            )}
            <header className="flex justify-between mb-8 font-medium text-gray-800 dark:text-gray-100">
                Vendor Details
                {user.vendor?.status === "pending" && (
                    <div className=" badge badge-warning">
                        {user.vendor.status_label}
                    </div>
                )}
                {user.vendor?.status === "reject" && (
                    <div className=" badge badge-error">
                        {user.vendor.status_label}
                    </div>
                )}
                {user.vendor?.status === "approved" && (
                    <div className=" badge badge-success text-white">
                        {user.vendor.status_label}
                    </div>
                )}
            </header>
            <div>
                {!user.vendor && (
                    <PrimaryButton
                        onClick={() => setShowBecomeVendorConfirmation(true)}
                        disabled={processing}
                    >
                        Become a vendor
                    </PrimaryButton>
                )}
            </div>
            <div>
                {user.vendor && (
                    <>
                        <form onSubmit={updateVendor}>
                            <div className="mb-4">
                                <InputLabel value="Store Name" />
                                <TextInput
                                    id="name"
                                    className={"mt-1 block w-full"}
                                    value={data.store_name}
                                    onChange={onStoreNameChange}
                                    required
                                />
                                <InputError
                                    message={errors.store_name}
                                    className="mt-2"
                                />
                            </div>
                            <div className="mb-4">
                                <InputLabel value="Store Address" />
                                <textarea
                                    className=" textarea textarea-bordered w-full mt-1"
                                    value={data.store_address ?? ''}
                                    placeholder="Enter Your Store Address"
                                    onChange={(
                                        ev: React.ChangeEvent<HTMLTextAreaElement>
                                    ) => setData("store_address", ev.target.value)}
                                ></textarea>
                                <InputError message={errors.store_address} />
                            </div>
                            <div className="flex items-center gap-4">
                                <PrimaryButton disabled={processing}>
                                    Update
                                </PrimaryButton>
                            </div>
                        </form>
                        <form
                            method="post"
                            className="my-8"
                        >
                            <input type="hidden" name="_token" value={token} />
                        </form>
                        {user.stripe_account_active && (
                            <div className="text-center text-gray-400  my-4 text-sm">
                                Your are successfully connected to Stripe
                            </div>
                        )}
                        <button
                            disabled={user.stripe_account_active}
                            className="btn btn-primary"
                            onClick={(ev)=>connect(ev)}
                        >
                            Connect to stripe
                        </button>
                    </>
                )}
            </div>
            <Modal show={showBecomeVendorConfirmation} onClose={closeModal}>
                <form className="p-8" onSubmit={becomeVendor}>
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Are you sure you want to become a vendor
                    </h2>
                    <div className="flex justify-end">
                        <SecondaryButton onClick={closeModal}>Cancel</SecondaryButton>
                        <PrimaryButton disabled={processing}>Confirm</PrimaryButton>
                    </div>
                </form>
            </Modal>
        </section>
    );
}
