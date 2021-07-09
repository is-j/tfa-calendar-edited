import { useState, useEffect, useRef } from 'react'
import { usePage } from '@inertiajs/inertia-react'
import { Transition } from '@headlessui/react'
import { XIcon } from '@heroicons/react/outline'
const Alert = () => {
    const { alert } = usePage().props
    const [open, setOpen] = useState(false)
    const closeButtonRef = useRef(null)
    useEffect(() => {
        if (alert.message) {
            setOpen(true)
        }
    }, [alert.updated])
    return (
        <Transition
            show={open}
            enter="ease-out duration-300"
            enterFrom="opacity-0"
            enterTo="opacity-100"
            leave="ease-in duration-200"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            className="fixed mx-auto top-[4.75rem] left-0 right-0 z-10 max-w-lg w-full select-none px-3"
        >
            <div className="text-white px-6 py-4 rounded-lg shadow-lg bg-gray-700">
                <div className="flex justify-between items-center">
                    <div className="pr-2">
                        {alert.message && alert.message}
                    </div>
                    <button ref={closeButtonRef} type="button" className="focus:outline-none" onClick={() => setOpen(false)}>
                        <XIcon className="h-6 w-6" />
                    </button>
                </div>
            </div>
        </Transition>
    )
}

export default Alert
