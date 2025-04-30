import ApplicationLogo from "@/Components/app/ApplicationLogo";
import Navbar from "@/Components/app/Navbar";
import Dropdown from "@/Components/core/Dropdown";
import NavLink from "@/Components/core/NavLink";
import ResponsiveNavLink from "@/Components/core/ResponsiveNavLink";
import { Link, usePage } from "@inertiajs/react";
import { PropsWithChildren, ReactNode, useState } from "react";

export default function AuthenticatedLayout({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);
    const props = usePage().props;
    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <Navbar />
            {props.error && (
                <div className="container mx-auto px-8 mt-8">
                    <div className="alert alert-error">{props.error}</div>
                </div>
            )}

            <main>{children}</main>
        </div>
    );
}
