import { XCircleIcon } from "@heroicons/react/24/outline";
import React from "react";

type Props = {};

export default function Failure({}: Props) {
    return (
        <div className="mx-auto flex flex-col items-center">
            <div>
                <XCircleIcon className="size-24 text-red-500" />
            </div>
            <div>Unexpected error occur. Order Fail.</div>
        </div>
    );
}
