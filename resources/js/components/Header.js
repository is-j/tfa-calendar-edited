import { Children, cloneElement } from 'react'

const Header = props => {
    return (
        <header className="bg-white shadow rounded-md py-6 px-4 sm:px-6 lg:px-8 flex flex-nowrap space-x-3 items-center">
            <div>
                {Children.map(props.children, child => {
                    if (child.type.displayName === 'Icon')
                        return cloneElement(child.props.children, {
                            className:
                                'h-12 w-12 text-white bg-gray-800 rounded-lg p-2',
                        })
                })}
            </div>
            <h1 className="text-3xl font-bold text-gray-900 uppercase">
                {Children.map(props.children, child => {
                    if (child.type.displayName === 'Title') return child
                })}
            </h1>
        </header>
    )
}

const Title = ({ children }) => children
Title.displayName = 'Title'
Header.Title = Title

const Icon = ({ children }) => children
Icon.displayName = 'Icon'
Header.Icon = Icon

export default Header
