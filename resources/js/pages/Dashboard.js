import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import { AdjustmentsIcon } from '@heroicons/react/outline'

const Dashboard = () => {
    return (
        <AppLayout>
            <Header>
                <Header.Title>Dashboard</Header.Title>
                <Header.Icon><AdjustmentsIcon /></Header.Icon>
            </Header>
        </AppLayout>
    )
}

export default Dashboard