import { InertiaLink } from '@inertiajs/inertia-react'
import Favicon from '../../images/favicon.png'

const Brand = (props) => {
    return (
        <div className={props.className}>
            <InertiaLink href="/">
                <div className="bg-gray-800 rounded-lg px-3 h-14 flex items-center space-x-3 shadow">
                    <img className="h-8 w-8" src={Favicon}></img>
                    <span className="text-2xl select-none text-[#FFF7AE] font-black tracking-wide uppercase">Flash</span>
                </div>
            </InertiaLink>
        </div>
    )
}

export default Brand
