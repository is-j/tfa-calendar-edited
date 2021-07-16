import { Fragment, useRef, useState, useEffect } from 'react'
import { InertiaLink, usePage } from '@inertiajs/inertia-react'
import { DateTime } from 'luxon'
import NProgress from 'nprogress'
import '../../css/nprogress.css'
import { Dialog, Transition } from '@headlessui/react'
import { AdjustmentsIcon, EyeIcon } from '@heroicons/react/outline'
import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import UnclaimEventForm from '../components/forms/UnclaimEventForm'
import ViewEventForm from '../components/forms/ViewEventForm'

const Modal = (props) => {
    const { user } = usePage().props
    const [eventClickInfo, setEventClickInfo] = useState(null)
    const [open, setOpen] = useState(false)
    const backButton = useRef(null)
    const formRef = useRef(null)
    useEffect(() => {
        if (eventClickInfo !== null) setOpen(true)
    }, [eventClickInfo])
    const getEventClickInfo = () => {
        NProgress.start()
        fetch(`/events/${props.event.id}`, {
            method: 'GET'
        }).then(response => response.json()).then(data => {
            setEventClickInfo(data)
            NProgress.done()
            NProgress.remove()
        })
    }
    return (<>
        <button className="text-left" type="button" onClick={getEventClickInfo}> {props.children}</button>
        <Transition.Root show={open} as={Fragment}>
            <Dialog
                as="div"
                static
                className="fixed z-10 inset-0 overflow-y-auto"
                initialFocus={backButton}
                open={open}
                onClose={setOpen}
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
                                        {user.role_name === 'tutor' ?
                                            (<ViewEventForm ref={formRef} event={eventClickInfo} onSuccess={() => setOpen(false)} />) :
                                            (<UnclaimEventForm ref={formRef} event={eventClickInfo} onSuccess={() => setOpen(false)} />)}
                                    </div>
                                </div>
                            </div>
                            <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    type="button"
                                    className="w-full inline-flex justify-center btn-negative px-4 py-2 sm:ml-3 sm:w-auto sm:text-sm"
                                    onClick={() => formRef.current.requestSubmit()}
                                >
                                    {user.role_name === 'tutor' ? 'Delete' : 'Unclaim'}
                                </button>
                                <button
                                    type="button"
                                    className="mt-3 w-full inline-flex justify-center btn-neutral px-4 py-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                    onClick={() => setOpen(false)}
                                    ref={backButton}
                                >
                                    Back
                                </button>
                            </div>
                        </div>
                    </Transition.Child>
                </div>
            </Dialog>
        </Transition.Root>
    </>)
}

const Dashboard = () => {
    const { user, nextEvent, todayEvents } = usePage().props
    return (
        <AppLayout>
            <Header>
                <Header.Title>Dashboard</Header.Title>
                <Header.Icon><AdjustmentsIcon /></Header.Icon>
            </Header>
            <div className={`grid grid-cols-1 mt-3 ${!nextEvent && !todayEvents ? '' : 'sm:grid-cols-2 gap-3'}`}>
                {!nextEvent && !todayEvents ? (<>
                    <div className="bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8">
                        {user.role_name === 'tutor' ?
                            (<h3 className="text-base">You have no sessions today or in the nearby future, but you can create more open sessions <InertiaLink className="link-inline" href="/calendar">here</InertiaLink>!</h3>) :
                            (<h3 className="text-base">You have no sessions today or in the nearby future, but you can sign up for more sessions <InertiaLink className="link-inline" href="/calendar">here</InertiaLink>!</h3>)}
                    </div>
                </>) : (<>
                    <div className="bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8">
                        <h2 className="uppercase text-gray-500 text-xl mb-3">Sessions today</h2>
                        {todayEvents && todayEvents.map(event => (
                            <Modal key={event.id} event={event}>
                                <div className="flex items-center py-1 px-3 space-x-6 rounded hover:bg-gray-100">
                                    <div className="flex items-center space-x-3 flex-shrink-0 py-1 px-3 rounded-md bg-gray-100">
                                        <div className="rounded-full bg-blue-500 h-3 w-3"></div>
                                        <span>{DateTime.fromISO(event.start).toFormat('t')}</span>
                                    </div>
                                    <span className="sm:text-lg">{user.role_name === 'tutor' ? 'Teaching' : 'Learning'} {event.subject_name} {user.role_name === 'tutor' ? `to ${event.student_name}` : `from ${event.tutor_name}`}</span>
                                </div>
                            </Modal>
                        ))}
                        {todayEvents ? (<></>) : user.role_name === 'student' ?
                            (<h3 className="text-base">None, but you can sign up for one <InertiaLink className="link-inline" href="/calendar">here</InertiaLink>!</h3>) :
                            (<h3 className="text-base">None, but you can create more open sessions <InertiaLink className="link-inline" href="/calendar">here</InertiaLink>!</h3>)}
                    </div>
                    <div className="bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8">
                        <h2 className="uppercase text-gray-500 text-xl mb-3">Next session</h2>
                        {nextEvent ?
                            (<Modal key={nextEvent.id} event={nextEvent}>
                                <div className="flex items-center py-1 px-3 space-x-6 rounded hover:bg-gray-100">
                                    <div className="flex items-center space-x-3 flex-shrink-0 py-1 px-3 rounded-md bg-gray-100">
                                        <div className="rounded-full bg-blue-500 h-3 w-3"></div>
                                        <span>{DateTime.fromISO(nextEvent.start).toFormat('DDD')}</span>
                                    </div>
                                    <span className="sm:text-lg">{user.role_name === 'tutor' ? 'Teaching' : 'Learning'} {nextEvent.subject_name} {user.role_name === 'tutor' ? `to ${nextEvent.student_name}` : `from ${nextEvent.tutor_name}`}</span>
                                </div>
                            </Modal>) :
                            user.role_name === 'student' ?
                                (<h3 className="text-base">None, but you can sign up for one <InertiaLink className="link-inline" href="/calendar">here</InertiaLink>!</h3>) :
                                (<h3 className="text-base">None, but you can create more open sessions <InertiaLink className="link-inline" href="/calendar">here</InertiaLink>!</h3>)}
                    </div>
                </>)}
            </div>
        </AppLayout>
    )
}

export default Dashboard