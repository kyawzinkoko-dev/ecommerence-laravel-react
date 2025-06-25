import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import VendorDetail from './Partials/VendorDetail';

export default function Edit({
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Profile
                </h2>
            }
        >
            <Head title="Profile" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl grid grid-cols-1 md:grid-cols-3 p-3 gap-4 ">
                   
                        <div className="col-span-2 space-y-6">
                            <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                                <UpdateProfileInformationForm
                                    mustVerifyEmail={mustVerifyEmail}
                                    status={status}
                                    className="max-w-xl"
                                />
                            </div>

                            <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                                <UpdatePasswordForm className="max-w-xl" />
                            </div>

                            <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                                <DeleteUserForm className="max-w-xl" />
                            </div>
                        </div>
                        <div className='bg-white sm:rounded-lg p-4 sm:p-8 shadow'>
                            <VendorDetail/>
                        </div>
                    
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
