import {Link, usePage} from "@inertiajs/react";
import React from "react";
import MiniCartDropdown from "@/Components/MiniCartDropdown";

type Props = {};

export default function Navbar({}: Props) {
    const {auth, totalPrice, totalQuantity} = usePage().props || {};
    const {user} = auth || {};
    console.log(usePage().props.auth)

    return (
        <div>
            <div className="navbar bg-base-100">
                <div className="flex-1">
                    <Link href="/" className="btn btn-ghost text-xl">
                        LaraStore
                    </Link>
                </div>
                <div className="flex-none gap-4">
                    <MiniCartDropdown/>
                    {user && (
                        <div className="dropdown dropdown-end">
                            <div
                                tabIndex={0}
                                role="button"
                                className="btn btn-ghost btn-circle avatar"
                            >
                                <div className="w-10 rounded-full">
                                    <img
                                        alt="Tailwind CSS Navbar component"
                                        src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp"
                                    />
                                </div>
                            </div>
                            <ul
                                tabIndex={0}
                                className="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow"
                            >
                                <li>
                                    <Link
                                        href={route("profile.edit")}
                                        className="justify-between"
                                    >Profile
                                    </Link>
                                </li>
                                <li>
                                    <Link href={route("logout")} method="post">Logout</Link>
                                </li>
                            </ul>
                        </div>
                    )}
                    {!user && <div className="flex gap-4">
                        <Link href={route('login')} className="btn">Login</Link>
                        <Link href={route('register')} className="btn btn-primary"> Register</Link>
                    </div>}
                </div>
            </div>
        </div>
    );
}
