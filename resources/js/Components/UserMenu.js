import { InertiaLink } from '@inertiajs/inertia-react'
import React, { useEffect, useRef, useState } from 'react'

const UserMenu = () => {
    const [isOpen, setIsOpen] = useState(false)
    const btnRef = useRef(null)
    const menuRef = useRef(null)
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (isOpen && btnRef.current && !btnRef.current.contains(e.target) && menuRef.current && !menuRef.current.contains(e.target)) {
                setIsOpen(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => {
            document.removeEventListener('mousedown', handleClickOutside)
        }
    }, [menuRef, isOpen])
    return (
        <>
            <div>
                <button type='button' className='bg-gray-800 flex text-sm rounded-full focus:outline-none' id='user-menu' aria-expanded='false' aria-haspopup='true' ref={btnRef} onClick={() => setIsOpen(!isOpen)}>
                    <span className='sr-only'>Open user menu</span>
                    <svg className='h-10 w-10 text-white' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path strokeLinecap='round' strokeLinejoin='round' strokeWidth='2' d='M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z' />
                    </svg>
                </button>
            </div>
            <div className='origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none' role='menu' aria-orientation='vertical' aria-labelledby='user-menu' ref={menuRef} style={{ display: (isOpen ? 'block' : 'none') }}>
                <a className='block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100' href='#' role='menuitem'>Settings</a>
                <InertiaLink className='block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left focus:outline-none focus:border-0' href='/logout' method='post' as='button' type='button'>Log out</InertiaLink>
            </div>
        </>
    )
}

export default UserMenu
