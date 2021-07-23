import Navbar from '../components/Navbar'
import Footer from '../components/Footer'

const AppLayout = props => {
    return (
        <>
            <Navbar />
            <div className="flex flex-col min-h-screen">
                <div className="flex-grow mt-20">
                    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                        {props.children}
                    </div>
                </div>
                <Footer />
            </div>
        </>
    )
}

export default AppLayout
