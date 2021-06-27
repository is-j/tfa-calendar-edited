import { useEffect, useRef, Fragment, useState } from 'react'
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { Dialog, Transition } from '@headlessui/react'
import { PlusIcon, EyeIcon } from '@heroicons/react/outline'
import AppLayout from '../layouts/AppLayout'
import CreateSlotForm from '../components/forms/CreateSlotForm'
import ViewSlotForm from '../components/forms/ViewSlotForm'

const Calendar = () => {
    const mobileToolbarState = {
        left: 'title',
        right: 'prev,next'
    }
    const desktopToolbarState = {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    }
    const calendarRef = useRef(null)
    const [toolbarState, setToolbarState] = useState(window.innerWidth > 768 ? desktopToolbarState : mobileToolbarState)
    const [dateClickInfo, setDateClickInfo] = useState(null)
    const [eventClickInfo, setEventClickInfo] = useState(null)
    useEffect(() => {
        let prevWidth
        const checkWidth = () => {
            const currentWidth = window.innerWidth
            if ((currentWidth <= 768 && prevWidth >= 768) || (currentWidth <= 768 && prevWidth == null)) {
                calendarRef.current.getApi().changeView('timeGridDay')
                setToolbarState(mobileToolbarState)
            } else if (currentWidth >= 768 && prevWidth <= 768) {
                calendarRef.current.getApi().changeView('dayGridMonth')
                setToolbarState(desktopToolbarState)
            }
            prevWidth = window.innerWidth
        }
        window.addEventListener('resize', checkWidth)
        return () => {
            window.removeEventListener('resize', checkWidth)
        }
    }, [window])
    useEffect(() => {
        if (dateClickInfo !== null) setOpenCreateSlotModal(true)
    }, [dateClickInfo])
    useEffect(() => {
        if (eventClickInfo !== null) setOpenViewSlotModal(true)
    }, [eventClickInfo])

    // forms
    const createSlotFormRef = useRef(null)
    const viewSlotFormRef = useRef(null)

    // modals
    const cancelButtonCreateSlotModalRef = useRef(null)
    const cancelButtonViewSlotModalRef = useRef(null)
    const [openCreateSlotModal, setOpenCreateSlotModal] = useState(false)
    const [openViewSlotModal, setOpenViewSlotModal] = useState(false)
    return (
        <AppLayout>
            <FullCalendar
                ref={calendarRef}
                plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
                initialView={window.innerWidth > 768 ? 'dayGridMonth' : 'timeGridDay'}
                headerToolbar={toolbarState}
                timeZone='local'
                selectable={true}
                nowIndicator={true}
                lazyFetching={true}
                dateClick={info => setDateClickInfo(info)}
                eventClick={info => setEventClickInfo(info)}
                events={(info, successCallback) => {
                    fetch(`/api/slot/get/${1}?start=${encodeURIComponent(info.startStr)}&end=${encodeURIComponent(info.endStr)}`, {
                        method: 'GET'
                    }).then(response => response.json()).then(data => { successCallback(data) });
                }}
            />

            {/* createSlotModal */}
            <Transition.Root show={openCreateSlotModal} as={Fragment}>
                <Dialog
                    as="div"
                    static
                    className="fixed z-10 inset-0 overflow-y-auto"
                    initialFocus={cancelButtonCreateSlotModalRef}
                    open={openCreateSlotModal}
                    onClose={setOpenCreateSlotModal}
                >
                    <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <Dialog.Overlay className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                        </Transition.Child>
                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">
                            &#8203;
                        </span>
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enterTo="opacity-100 translate-y-0 sm:scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 translate-y-0 sm:scale-100"
                            leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <div className="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div className="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div className="sm:flex sm:items-start">
                                        <div className="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <PlusIcon className="h-6 w-6 text-blue-600" aria-hidden="true" />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <CreateSlotForm ref={createSlotFormRef} info={dateClickInfo} onSuccess={() => setOpenCreateSlotModal(false)} />
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        className="w-full inline-flex justify-center btn-positive px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() => createSlotFormRef.current.requestSubmit()}
                                    >
                                        Create
                                    </button>
                                    <button
                                        type="button"
                                        className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2  sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() => setOpenCreateSlotModal(false)}
                                        ref={cancelButtonCreateSlotModalRef}
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </Transition.Child>
                    </div>
                </Dialog>
            </Transition.Root>

            {/* viewSlotModal */}
            <Transition.Root show={openViewSlotModal} as={Fragment}>
                <Dialog
                    as="div"
                    static
                    className="fixed z-10 inset-0 overflow-y-auto"
                    initialFocus={cancelButtonViewSlotModalRef}
                    open={openViewSlotModal}
                    onClose={setOpenViewSlotModal}
                >
                    <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <Dialog.Overlay className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                        </Transition.Child>
                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">
                            &#8203;
                        </span>
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enterTo="opacity-100 translate-y-0 sm:scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 translate-y-0 sm:scale-100"
                            leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <div className="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div className="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div className="sm:flex sm:items-start">
                                        <div className="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <EyeIcon className="h-6 w-6 text-blue-600" aria-hidden="true" />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <ViewSlotForm ref={viewSlotFormRef} info={eventClickInfo} onSuccess={() => setOpenViewSlotModal(false)} />
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        className="w-full inline-flex justify-center btn-negative px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() => viewSlotFormRef.current.requestSubmit()}
                                    >
                                        Delete
                                    </button>
                                    <button
                                        type="button"
                                        className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2  sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() => setOpenViewSlotModal(false)}
                                        ref={cancelButtonViewSlotModalRef}
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </Transition.Child>
                    </div>
                </Dialog>
            </Transition.Root>
        </AppLayout>
    )
}

export default Calendar
