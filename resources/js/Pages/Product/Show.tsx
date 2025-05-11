import { Product, VariationTypeOption } from "@/types";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import React, { useEffect, useMemo, useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Carousel from "@/Components/core/Carousel";
import CurrencyFormatter from "@/Components/core/CurrencyFormatter";
import { arrayAreEqual } from "@/helper";

function Show({
    product,
    variationOptions,
}: {
    product: Product;
    variationOptions: number[];
}) {
    const form = useForm<{
        option_ids: Record<string, number>;
        quantity: number;
        price: number | null;
    }>({
        option_ids: {},
        quantity: 1,
        price: null,
    });
    const { url } = usePage();
    //
    const [selectedOption, setSelectedOption] = useState<
        Record<number, VariationTypeOption>
    >([]);
    //
    const images = useMemo(() => {
        for (let typeId in selectedOption) {
            let option = selectedOption[typeId];
            if (option.images.length > 0) return product.images;
        }
        return product.images;
    }, [product, selectedOption]);

    //
    const computedProduct = useMemo(() => {
        console.log(selectedOption);
        const selectedOptionIds = Object.values(selectedOption)
            .map((op) => op.id)
            .sort();
        for (let variation of product.variations) {
            const optionIds = variation.variations_type_option_ids.sort();
            if (arrayAreEqual(selectedOptionIds, optionIds)) {
                return {
                    price: variation.price,
                    quantity:
                        variation.quantity === null
                            ? Number.MAX_VALUE
                            : variation.quantity,
                };
            }
        }
        return {
            price: product.price,
            quantity: product.quantity,
        };
    }, [product, selectedOption]);
    console.log(computedProduct);
    //
    useEffect(() => {
        for (let type of product.variationTypes) {
            const selectedOptionId: number = variationOptions[type.id];
            chooseOption(
                type.id,
                type.options.find((op) => op.id === selectedOptionId) ||
                    type.options[0],
                false
            );
        }
    }, []);
    useEffect(() => {
        const idsMap = Object.fromEntries(
            Object.entries(selectedOption).map(
                ([typeId, option]: [string, VariationTypeOption]) => [
                    typeId,
                    option.id,
                ]
            )
        );

        form.setData("option_ids", idsMap);
    }, [selectedOption]);
    //
    const getOptionIdsMap = (newOption: object) => {
        return Object.fromEntries(
            Object.entries(newOption).map(([a, b]) => [a, b.id])
        );
    };

    const chooseOption = (
        typeId: number,
        option: VariationTypeOption,
        updateRouter: boolean = true
    ) => {
        setSelectedOption((prevSelectedOptions) => {
            const newOption = {
                ...prevSelectedOptions,
                [typeId]: option,
            };
            if (updateRouter) {
                router.get(
                    url,
                    { options: getOptionIdsMap(newOption) },
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );
            }
            return newOption;
        });
    };
    const onQuantityChange = (ev: React.ChangeEvent<HTMLSelectElement>) => {
        form.setData("quantity", parseInt(ev.target.value));
    };
    const addToCart = () => {
        form.post(route("cart.store", product.id), {
            preserveScroll: true,
            preserveState: true,
            onError: (er) => {
                console.log(er);
            },
        });
    };

    const renderProductVariationType = () => {
        return product.variationTypes.map((type, i) => (
            <div key={type.id}>
                <b>{type.name}</b>
                {type.type === "Image" && (
                    <div className="flex gap-2 mb-4">
                        {type.options.map((option) => (
                            <div
                                key={option.id}
                                onClick={() => chooseOption(type.id, option)}
                            >
                                {option.images && (
                                    <img
                                        src={option.images[0].thumb}
                                        alt={option.name}
                                        onClick={() =>
                                            chooseOption(type.id, option)
                                        }
                                        className={
                                            "w-[50px] h-auto" +
                                            (selectedOption[type.id]?.id ===
                                                option.id)
                                                ? " outline outline-4 outline-primary"
                                                : ""
                                        }
                                    />
                                )}
                            </div>
                        ))}
                    </div>
                )}
                {type.type === "Radio" && (
                    <div className="flex join mb-4">
                        {type.options.map((op) => (
                            <input
                                type={"radio"}
                                className={"join-item btn"}
                                onChange={() => chooseOption(type.id, op)}
                                key={op.id}
                                value={op.id}
                                checked={selectedOption[type.id]?.id === op.id}
                                aria-label={op.name}
                            />
                        ))}
                    </div>
                )}
            </div>
        ));
    };
    const renderAddToCartButton = () => {
        return (
            <div className="mb-8 flex gap-8">
                <select
                    value={form.data.quantity}
                    onChange={onQuantityChange}
                    className={"select select-bordered w-full"}
                >
                    {Array.from({
                        length: Math.min(10, computedProduct.quantity),
                    }).map((el, i) => (
                        <option value={i + 1} key={i + 1}>
                            Quantity : {i + 1}{" "}
                        </option>
                    ))}
                </select>
                <button
                    onClick={addToCart}
                    disabled={computedProduct.quantity < 0}
                    className={"btn btn-primary"}
                >
                    Add to Cart
                </button>
            </div>
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title={product.title} />
            <div className="container mx-auto p-8">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div className="col-span-7">
                        <Carousel images={images} />
                    </div>

                    <div className="col-span-5">
                        <h2 className="text-2xl mb-8">{product.title}</h2>
                        <div className="text-2xl font-semibold">
                            <CurrencyFormatter amount={product.price} />
                        </div>
                        {renderProductVariationType()}
                        {computedProduct.quantity}
                        {computedProduct.quantity !== undefined &&
                            computedProduct.quantity > 0 &&
                            computedProduct.quantity < 10 && (
                                <div className={"text-error my-4"}>
                                    Only {computedProduct.quantity} left
                                </div>
                            )}
                        {computedProduct.quantity < 0 && (
                            <div className="text-error my-4">Out Of Stock</div>
                        )}
                        {renderAddToCartButton()}
                        <b className="text-xl">About Item</b>
                        <div
                            className="wysiwyg-output"
                            dangerouslySetInnerHTML={{
                                __html: product.description,
                            }}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

export default Show;
