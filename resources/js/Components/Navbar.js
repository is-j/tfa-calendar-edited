import { runInContext } from 'lodash'
import React, { useState } from 'react'
import Logo from './Logo'
import UserMenu from './UserMenu'
import NavMenu from './NavMenu'

const Navbar = () => {
    const [isOpen, setIsOpen] = useState(false)
    const [isLoading, setIsLoading] = useState(false)
    return (
        <nav className='bg-gray-800 fixed w-full z-10 shadow-md'>
            <div className='max-w-7xl mx-auto px-2 sm:px-6 lg:px-8'>
                <div className='relative flex items-center justify-between h-16'>
                    <div className='absolute inset-y-0 left-0 flex items-center sm:hidden'>
                        <button type='button' className='inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none' aria-controls='mobile-menu' aria-expanded='false' onClick={() => setIsOpen(!isOpen)}>
                            <span className='sr-only'>Open main menu</span>
                            <svg className='block h-6 w-6' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'>
                                <path strokeLinecap='round' strokeLinejoin='round' strokeWidth='2' d={isOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'} />
                            </svg>
                        </button>
                    </div>
                    <div className='flex-1 flex items-center justify-center sm:items-stretch sm:justify-start'>
                        <div className='flex-shrink-0 flex items-center'>
                            <Logo />
                        </div>
                        <div className='hidden sm:block sm:ml-6 my-auto'>
                            <div className='flex space-x-4 justify-center'>
                                <NavMenu type='desktop' />
                            </div>
                        </div>
                    </div>
                    <div className='absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0'>
                        <div className='bg-gray-700 text-white px-3 py-2 rounded-md text-base font-medium mr-3 hidden sm:block'>
                            {isLoading ?
                                <svg className='animate-spin h-7 w-7 text-white' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                                    <circle className='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' strokeWidth='4'></circle>
                                    <path className='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
                                </svg>
                                :
                                <svg className='h-7 w-7 text-white' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                    <path strokeLinecap='round' strokeLinejoin='round' strokeWidth='3' d='M5 13l4 4L19 7' />
                                </svg>
                            }
                        </div>
                        <div className='ml-3 relative'>
                            <UserMenu />
                        </div>
                    </div>
                </div>
            </div>
            <div className='sm:hidden' style={{ display: (isOpen ? 'block' : 'none') }}>
                <div className='px-2 pt-2 pb-3 space-y-1'>
                    <NavMenu type='mobile' />
                </div>
            </div>
        </nav>
    )
}

export default Navbar
