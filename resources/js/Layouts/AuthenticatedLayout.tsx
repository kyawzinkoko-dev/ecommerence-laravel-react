import ApplicationLogo from "@/Components/app/ApplicationLogo";
import Navbar from "@/Components/app/Navbar";
import Dropdown from "@/Components/core/Dropdown";
import NavLink from "@/Components/core/NavLink";
import ResponsiveNavLink from "@/Components/core/ResponsiveNavLink";
import { Link, usePage } from "@inertiajs/react";
import { log } from "console";
import {
    PropsWithChildren,
    ReactNode,
    useEffect,
    useRef,
    useState,
} from "react";

export default function AuthenticatedLayout({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);
    const props = usePage().props;
    const [successMessage, setSuccessMessage] = useState<any[]>([]);
    const timeoutRef = useRef<{ [key: number]: ReturnType<typeof setTimeout> }>(
        {}
    );

    console.log(successMessage);
    useEffect(() => {
        if (props.success.message) {
            const newMessage = {
                ...props.success,
                id: props.success.time,
            };
            setSuccessMessage((prevMsg) => [newMessage, ...prevMsg]);

            const timeOutId = setTimeout(() => {
                console.log("here");
                setSuccessMessage((prevMsg) =>
                    prevMsg.filter((msg) => msg.id !== newMessage.id)
                );
                console.log(successMessage);
                delete timeoutRef.current[newMessage.id];
            }, 5000);
            timeoutRef.current[newMessage.id] = timeOutId;
        }
    }, [props.success]);
    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <Navbar />
            {props.error && (
                <div className="container mx-auto px-8 mt-8">
                    <div className="alert alert-error">{props.error}</div>
                </div>
            )}

            {successMessage.length > 0 && (
                <div className="toast toast-top toast-end z-[1000] mt-16">
                    {successMessage.map((msg) => (
                        <div key={msg.id} className="alert alert-success">
                            <span>{msg.message}</span>
                        </div>
                    ))}
                </div>
            )}
            <main>{children}</main>
        </div>
    );
}
