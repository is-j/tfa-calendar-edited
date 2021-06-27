import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import { XCircleIcon } from '@heroicons/react/outline'
import { usePage } from '@inertiajs/inertia-react'

const Cancel = () => {
    const { slot_id } = usePage().props
    return (
        <AppLayout>
            <Header>
                <Header.Title>Cancel</Header.Title>
                <Header.Icon><XCircleIcon /></Header.Icon>
            </Header>
            {slot_id}
        </AppLayout>
    )
}

export default Cancel