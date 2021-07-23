import { Children } from 'react'
import Brand from '../components/Brand'
import Footer from '../components/Footer'

const AuthLayout = props => {
    return (
        <div className="flex flex-col justify-between min-h-screen">
            <main className="flex-grow flex justify-center items-center">
                <div className="my-auto w-full mx-3 sm:mx-auto sm:w-96 rounded-md shadow-lg py-6 px-3 bg-white">
                    <div className="flex flex-wrap justify-center mb-6">
                        <Brand className="mr-3 hidden sm:block" minimized />
                        <div className="px-3 bg-gray-800 rounded-lg flex items-center h-14">
                            <span className="text-2xl uppercase text-[#FFF7AE]">
                                {Children.map(props.children, child => {
                                    if (child.type.displayName === 'Title')
                                        return child
                                })}
                            </span>
                        </div>
                    </div>
                    {Children.map(props.children, child => {
                        if (child.type.displayName === 'Content') return child
                    })}
                </div>
            </main>
            <Footer />
        </div>
    )
}

const Title = ({ children }) => children
Title.displayName = 'Title'
AuthLayout.Title = Title

const Content = ({ children }) => children
Content.displayName = 'Content'
AuthLayout.Content = Content

export default AuthLayout
