import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import { UserGroupIcon } from '@heroicons/react/outline'

const Tutors = () => {
    return (
        <AppLayout>
            <Header>
                <Header.Title>Tutors</Header.Title>
                <Header.Icon><UserGroupIcon /></Header.Icon>
            </Header>
        </AppLayout>
    )
}

export default Tutors