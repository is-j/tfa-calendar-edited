import React from 'react'
import AppLayout from '../Layouts/AppLayout'

const Dashboard = () => {
    return (
        <AppLayout>
            <div className='grid grid-cols-3 gap-4 w-11/12 mx-auto'>
                <div className='mx-auto rounded-lg shadow-lg bg-white h-full w-full py-3 px-6'>
                    <h2 className='text-xl'>Upcoming sessions</h2>
                    <div className='w-full border-b-2 border-gray-200'></div>

                </div>
            </div>
        </AppLayout>
    )
}

export default Dashboard
