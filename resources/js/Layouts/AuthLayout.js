import React from 'react'
import Footer from '../Components/Footer'

const AuthLayout = ({ children }) => {
    return (
        <div className='flex flex-col justify-between h-screen'>
            <main className='flex h-full'>
                <div className='my-auto w-full mx-3 sm:mx-auto sm:w-96 rounded-md shadow-lg py-6 px-3 bg-white'>
                    {children}
                </div>
            </main>
            <Footer />
        </div>
    )
}

export default AuthLayout
