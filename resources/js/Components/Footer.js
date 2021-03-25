import React from 'react'

const Footer = () => {
    return (
        <footer className='mt-4 pb-3'>
            <p className='text-center text-gray-400'>&copy; {(new Date()).getFullYear()} Dennis Eum, TFA</p>
        </footer>
    )
}

export default Footer
