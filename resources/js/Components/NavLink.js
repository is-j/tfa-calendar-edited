import { InertiaLink } from '@inertiajs/inertia-react'
import React from 'react'

const NavLink = ({ className, routeName, children }) => {
    const isActive = route().current(routeName)
    return (
        <InertiaLink className={`px-3 py-2 rounded-md text-sm font-medium ${isActive ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'} ${className}`} href={route(routeName)}>{children}</InertiaLink>
    )
}

export default NavLink
