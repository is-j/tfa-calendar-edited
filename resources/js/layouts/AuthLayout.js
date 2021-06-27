import Footer from '../components/Footer'

const AuthLayout = (props) => {
    return (
        <div className="flex flex-col justify-between min-h-screen">
            <main className="flex-grow mt-20">
                <div className="my-auto w-full mx-3 sm:mx-auto sm:w-96 rounded-md shadow-lg py-6 px-3 bg-white">
                    {props.children}
                </div>
            </main>
            <Footer />
        </div>
    )
}

export default AuthLayout