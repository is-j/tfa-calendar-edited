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
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                <div className="bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8">
                    <h2 className="uppercase text-gray-500 text-xl">Next sessions</h2>
                    <div className="flex justify-between items-center shadow-md py-1 px-3 rounded bg-gray-50">
                        <span className="uppercase sm:text-lg">date</span>
                        <span></span>
                    </div>
                </div>
                <div className="bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8">
                    <h2 className="uppercase text-gray-500 text-xl">Sessions today</h2>
                </div>
            </div>
        </AppLayout>
    )
}

export default Dashboard