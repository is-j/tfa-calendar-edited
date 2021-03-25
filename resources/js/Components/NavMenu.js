import React from 'react'
import NavLink from './NavLink'

const NavMenu = ({ type }) => {
    const typeClasses = (type === 'mobile') ? 'block' : ''
    return (
        <>
            <NavLink className={typeClasses} routeName='dashboard'>Dashboard</NavLink>
            <NavLink className={typeClasses} routeName='calendar'>Calendar</NavLink>
        </>
    )
}

export default NavMenu
