import Navbar from '../components/Navbar'
import Footer from '../components/Footer'

const AppLayout = (props) => {
    return (
        <div className="flex flex-col justify-between min-h-screen">
            <Navbar />
            <main className="flex-grow mt-20">
                <div className="max-w-7xl mx-auto pb-6 px-3 sm:px-6 lg:px-8 h-adjust md:h-full">
                    {props.children}
                </div>
            </main>
            <Footer />
        </div>
    )
}

export default AppLayout
