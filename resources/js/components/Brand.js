import { Link } from '@inertiajs/inertia-react'
import Favicon from '../../images/favicon.png'

const Brand = props => {
    if (props.minimized) {
        return (
            <div className={props.className}>
                <Link href="/">
                    <div className="bg-gray-800 rounded-lg px-3 h-14 flex items-center shadow">
                        <img className="h-8 w-8" src={Favicon}></img>
                    </div>
                </Link>
            </div>
        )
    } else {
        return (
            <div className={props.className}>
                <Link href="/">
                    <div className="bg-[#FF8C00] rounded-lg px-3 h-14 flex items-center space-x-3 shadow">
                        <img className="h-8 w-8" src={Favicon}></img>
                        <span className="text-2xl select-none text-[#FFD580] font-[black]tracking-wide uppercase">
                            Classbook
                        </span>
                    </div>
                </Link>
            </div>
        )
    }
}

export default Brand
