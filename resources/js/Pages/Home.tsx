import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {PageProps, PaginationTypeProps, Product} from "@/types";
import {Head} from "@inertiajs/react";
import ProductItem from "@/Components/app/ProductItem";

export default function Home({products}: PageProps<{ products: PaginationTypeProps<Product> }>) {
    const handleImageError = () => {
        document
            .getElementById("screenshot-container")
            ?.classList.add("!hidden");
        document.getElementById("docs-card")?.classList.add("!row-span-1");
        document
            .getElementById("docs-card-content")
            ?.classList.add("!flex-row");
        document.getElementById("background")?.classList.add("!hidden");
    };

    return (
        <AuthenticatedLayout>
            <Head title="Home"/>
            <div className="hero bg-base-200 min-h-screen">
                <div className="hero-content text-center">
                    <div className="max-w-md">
                        <h1 className="text-5xl font-bold">Hello there</h1>
                        <p className="py-6">
                            Provident cupiditate voluptatem et in. Quaerat
                            fugiat ut assumenda excepturi exercitationem quasi.
                            In deleniti eaque aut repudiandae et a id nisi.
                        </p>
                        <button className="btn btn-primary">Get Started</button>
                    </div>
                </div>

            </div>
            <div className={"grid grid-cols-3 gap-4 p-3 "}>
                {products.data.map((product, index) => (
                    <ProductItem product={product} key={product.id}/>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
