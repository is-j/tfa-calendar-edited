import React from 'react'
import Footer from '../Components/Footer'
import Navbar from '../Components/Navbar'

const AppLayout = ({ children }) => {
    return (
        <>
            <Navbar />
            <div className='flex flex-col justify-between h-screen'>
                <main className='mt-20 h-full'>
                    {children}
                </main>
                <Footer />
            </div>
        </>
    )
}

export default AppLayout
