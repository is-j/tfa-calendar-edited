import { useEffect, useRef, Fragment, useState } from 'react'
import { Head, usePage } from '@inertiajs/inertia-react'
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { Dialog, Transition } from '@headlessui/react'
import { PlusIcon, EyeIcon } from '@heroicons/react/outline'
import NProgress from 'nprogress'
import '../../css/nprogress.css'
import AppLayout from '../layouts/AppLayout'
import CreateEventForm from '../components/forms/CreateEventForm'
import ViewEventForm from '../components/forms/ViewEventForm'
import ClaimEventForm from '../components/forms/ClaimEventForm'
import UnclaimEventForm from '../components/forms/UnclaimEventForm'
import Alert from '../components/Alert'

const Calendar = () => {
    const mobileToolbarState = {
        left: 'title',
        right: 'prev,next',
    }
    const desktopToolbarState = {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    }
    const { user } = usePage().props
    const calendarRef = useRef(null)
    const [toolbarState, setToolbarState] = useState(
        window.innerWidth > 768 ? desktopToolbarState : mobileToolbarState
    )
    const [dateClickInfo, setDateClickInfo] = useState(null)
    const [eventClickInfo, setEventClickInfo] = useState(null)
    useEffect(() => {
        let prevWidth
        const checkWidth = () => {
            const currentWidth = window.innerWidth
            if (
                (currentWidth <= 768 && prevWidth >= 768) ||
                (currentWidth <= 768 && prevWidth == null)
            ) {
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
        if (dateClickInfo !== null) setOpenCreateEventModal(true)
    }, [dateClickInfo])
    useEffect(() => {
        if (eventClickInfo !== null) {
            if (user.role_name === 'speaker') {
                setOpenViewEventModal(true)
            } else if (user.role_name === 'teacher') {
                if (eventClickInfo.student_name) {
                    setOpenUnclaimEventModal(true)
                } else {
                    setOpenClaimEventModal(true)
                }
            }
        }
    }, [eventClickInfo])

    // Forms
    const createEventFormRef = useRef(null)
    const viewEventFormRef = useRef(null)
    const claimEventFormRef = useRef(null)
    const unclaimEventFormRef = useRef(null)

    // Modals
    const [openCreateEventModal, setOpenCreateEventModal] = useState(false)
    const [openViewEventModal, setOpenViewEventModal] = useState(false)
    const [openClaimEventModal, setOpenClaimEventModal] = useState(false)
    const [openUnclaimEventModal, setOpenUnclaimEventModal] = useState(false)
    const backButtonCreateEventModalRef = useRef(null)
    const backButtonViewEventModalRef = useRef(null)
    const backButtonClaimEventModalRef = useRef(null)
    const backButtonUnclaimEventModalRef = useRef(null)

    const getEventClickInfo = id => {
        NProgress.start()
        fetch(`/events/${id}`, {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => {
                setEventClickInfo(data)
                NProgress.done()
                NProgress.remove()
            })
    }
    return (
        <AppLayout>
            <Head>
                <title>Calendar &middot; Tutoring for All Calendar</title>
                <meta name="author" content="Dennis Eum"></meta>
                <meta name="robots" content="none"></meta>
            </Head>
            <Alert />
            <div className="h-[calc(100vh-132px)] md:h-full">
                <FullCalendar
                    ref={calendarRef}
                    plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
                    initialView={
                        window.innerWidth > 768 ? 'dayGridMonth' : 'timeGridDay'
                    }
                    headerToolbar={toolbarState}
                    timeZone="local"
                    selectable={true}
                    nowIndicator={true}
                    lazyFetching={true}
                    dateClick={info => {
                        if (user.role_name === 'tutor') setDateClickInfo(info)
                    }}
                    eventClick={info => getEventClickInfo(info.event.id)}
                    events={(info, successCallback) => {
                        if (
                            !openCreateEventModal &&
                            !openViewEventModal &&
                            !openClaimEventModal &&
                            !openUnclaimEventModal
                        ) {
                            NProgress.start()
                            fetch(
                                `/events?start=${encodeURIComponent(
                                    info.startStr
                                )}&end=${encodeURIComponent(info.endStr)}`,
                                {
                                    method: 'GET',
                                }
                            )
                                .then(response => response.json())
                                .then(data => {
                                    successCallback(data)
                                    NProgress.done()
                                    NProgress.remove()
                                })
                        }
                    }}
                />
            </div>

            {/* createEventModal */}
            <Transition.Root show={openCreateEventModal} as={Fragment}>
                <Dialog
                    as="div"
                    static
                    className="fixed z-10 inset-0 overflow-y-auto"
                    initialFocus={backButtonCreateEventModalRef}
                    open={openCreateEventModal}
                    onClose={setOpenCreateEventModal}
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
                        <span
                            className="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"
                        >
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
                                            <PlusIcon
                                                className="h-6 w-6 text-blue-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <CreateEventForm
                                                ref={createEventFormRef}
                                                info={dateClickInfo}
                                                onSuccess={() =>
                                                    setOpenCreateEventModal(
                                                        false
                                                    )
                                                }
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        className="w-full inline-flex justify-center btn-positive px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            createEventFormRef.current.requestSubmit()
                                        }
                                    >
                                        Create
                                    </button>
                                    <button
                                        type="button"
                                        className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2  sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            setOpenCreateEventModal(false)
                                        }
                                        ref={backButtonCreateEventModalRef}
                                    >
                                        Back
                                    </button>
                                </div>
                            </div>
                        </Transition.Child>
                    </div>
                </Dialog>
            </Transition.Root>

            {/* viewEventModal */}
            <Transition.Root show={openViewEventModal} as={Fragment}>
                <Dialog
                    as="div"
                    static
                    className="fixed z-10 inset-0 overflow-y-auto"
                    initialFocus={backButtonViewEventModalRef}
                    open={openViewEventModal}
                    onClose={setOpenViewEventModal}
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
                        <span
                            className="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"
                        >
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
                                            <EyeIcon
                                                className="h-6 w-6 text-blue-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <ViewEventForm
                                                ref={viewEventFormRef}
                                                event={eventClickInfo}
                                                onSuccess={() =>
                                                    setOpenViewEventModal(false)
                                                }
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        className="w-full inline-flex justify-center btn-negative px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            viewEventFormRef.current.requestSubmit()
                                        }
                                    >
                                        Delete
                                    </button>
                                    <button
                                        type="button"
                                        className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            setOpenViewEventModal(false)
                                        }
                                        ref={backButtonViewEventModalRef}
                                    >
                                        Back
                                    </button>
                                </div>
                            </div>
                        </Transition.Child>
                    </div>
                </Dialog>
            </Transition.Root>

            {/* claimEventModal */}
            <Transition.Root show={openClaimEventModal} as={Fragment}>
                <Dialog
                    as="div"
                    static
                    className="fixed z-10 inset-0 overflow-y-auto"
                    initialFocus={backButtonClaimEventModalRef}
                    open={openClaimEventModal}
                    onClose={setOpenClaimEventModal}
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
                        <span
                            className="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"
                        >
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
                                            <EyeIcon
                                                className="h-6 w-6 text-blue-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <ClaimEventForm
                                                ref={claimEventFormRef}
                                                event={eventClickInfo}
                                                onSuccess={() =>
                                                    setOpenClaimEventModal(
                                                        false
                                                    )
                                                }
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        className="w-full inline-flex justify-center btn-positive px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            claimEventFormRef.current.requestSubmit()
                                        }
                                    >
                                        Claim
                                    </button>
                                    <button
                                        type="button"
                                        className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            setOpenClaimEventModal(false)
                                        }
                                        ref={backButtonClaimEventModalRef}
                                    >
                                        Back
                                    </button>
                                </div>
                            </div>
                        </Transition.Child>
                    </div>
                </Dialog>
            </Transition.Root>

            {/* unclaimEventModal */}
            <Transition.Root show={openUnclaimEventModal} as={Fragment}>
                <Dialog
                    as="div"
                    static
                    className="fixed z-10 inset-0 overflow-y-auto"
                    initialFocus={backButtonUnclaimEventModalRef}
                    open={openUnclaimEventModal}
                    onClose={setOpenUnclaimEventModal}
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
                        <span
                            className="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"
                        >
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
                                            <EyeIcon
                                                className="h-6 w-6 text-blue-600"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <UnclaimEventForm
                                                ref={unclaimEventFormRef}
                                                event={eventClickInfo}
                                                onSuccess={() =>
                                                    setOpenUnclaimEventModal(
                                                        false
                                                    )
                                                }
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="button"
                                        className="w-full inline-flex justify-center btn-negative px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            unclaimEventFormRef.current.requestSubmit()
                                        }
                                    >
                                        Unclaim
                                    </button>
                                    <button
                                        type="button"
                                        className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        onClick={() =>
                                            setOpenUnclaimEventModal(false)
                                        }
                                        ref={backButtonUnclaimEventModalRef}
                                    >
                                        Back
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
