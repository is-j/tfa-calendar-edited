import { useRef } from 'react'
import { usePage, InertiaLink } from '@inertiajs/inertia-react'
import { XCircleIcon } from '@heroicons/react/outline'
import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import Main from '../components/Main'
import CancelEventForm from '../components/forms/CancelEventForm'


const Cancel = () => {
    const { event, alert } = usePage().props
    const formRef = useRef(null)
    return (
        <AppLayout>
            <Header>
                <Header.Title>Cancel</Header.Title>
                <Header.Icon><XCircleIcon /></Header.Icon>
            </Header>
            <Main>
                {event ? (<>
                    <CancelEventForm ref={formRef} event={event} />
                    <div className="grid grid-cols-1 sm:grid-cols-2 sm:grid-flow-row-dense gap-3">
                        <button className="btn-positive w-full h-12" type="button" onClick={() => formRef.current.requestSubmit()}>Cancel</button>
                        <InertiaLink className="btn-neutral w-full h-12 sm:order-first" href="/calendar" as="button" type="button">Back</InertiaLink>
                    </div>
                </>) : (<>
                    {alert.message}
                </>)}
            </Main>
        </AppLayout>
    )
}

export default Cancel