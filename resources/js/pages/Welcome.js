import { Fragment } from 'react'
import { Head, Link } from '@inertiajs/inertia-react'
import { Popover, Transition } from '@headlessui/react'
import {
    MenuIcon,
    XIcon,
    EyeIcon,
    GiftIcon,
    ShieldCheckIcon,
    LightningBoltIcon,
} from '@heroicons/react/outline'
import Brand from '../components/Brand'
import Footer from '../components/Footer'
import Example from '../../images/example.png'

const Welcome = () => {
    const features = [
        {
            name: 'Fast',
            description:
                'Every click is convenient and simple. The software is also constantly updated for efficiency.',
            icon: LightningBoltIcon,
        },
        {
            name: 'Visual',
            description:
                'You can easily interact with a calendar to manage all your sessions.',
            icon: EyeIcon,
        },
        {
            name: 'Free',
            description:
                'This software is sponsored by Digital Literacy Team, a branch under Robotics for All. That means you can use this at no cost!',
            icon: GiftIcon,
        },
        {
            name: 'Secure',
            description:
                'All information is handled with a secure connection, while the code is written with modern techniques and backed by security built into Laravel.',
            icon: ShieldCheckIcon,
        },
    ]
    return (
        <>
            <Head>
                <title>Welcome &middot; Robotics for All Calendar</title>
                <meta name="author" content="Digital Literacy Team"></meta>
                <meta
                    name="description"
                    content="This is sponsored software by Digital Literacy Team, which a educator can use to easily attend a speaker or influencer's event with their class!"
                />
                
                <meta name="robots" content="index, follow"></meta>
                <link
                    rel="canonical"
                    href="https://cal.roboticsforall.org"
                ></link>
            </Head>
            <div className="relative bg-[#FEF3C7] overflow-hidden">
                <div className="max-w-7xl mx-auto">
                    <div className="relative z-10 pb-8 bg-[#FEF3C7] sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                        <svg
                            className="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-[#FEF3C7] transform translate-x-1/2"
                            fill="currentColor"
                            viewBox="0 0 100 100"
                            preserveAspectRatio="none"
                            aria-hidden="true"
                        >
                            <polygon points="50,0 100,0 50,100 0,100" />
                        </svg>

                        <Popover>
                            {({ open }) => (
                                <>
                                    <div className="relative pt-6 px-4 sm:px-6 lg:px-8">
                                        <nav
                                            className="relative flex items-center justify-between sm:h-10 lg:justify-between"
                                            aria-label="Global"
                                        >
                                            <div className="flex items-center flex-grow flex-shrink-0 lg:flex-grow-0">
                                                <div className="flex items-center justify-between w-full md:w-auto">
                                                    <Brand />
                                                    <div className="-mr-2 flex items-center md:hidden">
                                                        <Popover.Button className="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                                                            <span className="sr-only">
                                                                Open main menu
                                                            </span>
                                                            <MenuIcon
                                                                className="h-6 w-6"
                                                                aria-hidden="true"
                                                            />
                                                        </Popover.Button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="hidden md:block md:ml-10 md:pr-4 md:space-x-8">
                                                <Link
                                                    href="/login"
                                                    className="font-xxlarge font-extrabold text-gray hover:text-gray-500"
                                                >
                                                    Log In
                                                </Link>
                                            </div>
                                        </nav>
                                    </div>

                                    <Transition
                                        show={open}
                                        as={Fragment}
                                        enter="duration-150 ease-out"
                                        enterFrom="opacity-0 scale-95"
                                        enterTo="opacity-100 scale-100"
                                        leave="duration-100 ease-in"
                                        leaveFrom="opacity-100 scale-100"
                                        leaveTo="opacity-0 scale-95"
                                    >
                                        <Popover.Panel
                                            focus
                                            static
                                            className="absolute top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden"
                                        >
                                            <div className="rounded-lg shadow-md bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                                                <div className="px-5 pt-4 flex items-center justify-between">
                                                    <div>
                                                        <Brand />
                                                    </div>
                                                    <div className="-mr-2">
                                                        <Popover.Button className="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                                                            <span className="sr-only">
                                                                Close main menu
                                                            </span>
                                                            <XIcon
                                                                className="h-6 w-6"
                                                                aria-hidden="true"
                                                            />
                                                        </Popover.Button>
                                                    </div>
                                                </div>
                                                <Link
                                                    href="/login"
                                                    className="block w-full px-5 py-3 text-center font-medium text-gray-600 bg-gray-50 hover:bg-gray-100"
                                                >
                                                    Log in
                                                </Link>
                                            </div>
                                        </Popover.Panel>
                                    </Transition>
                                </>
                            )}
                        </Popover>
                        <main className="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                            <div className="sm:text-center lg:text-center">
                                <h1 className="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                    <span className="block xl:inline">
                                        RFA Scheduler
                                    </span>
                                </h1>
                                <p className="mt-3 text-base text-gray sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                This is sponsored software by Digital Literacy Team, 
                                which a educator can use to easily attend a speaker or 
                                influencer's event with their class!
                                </p>
                                <div className="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-center">
                                    <div className="rounded-md shadow">
                                        <Link
                                            href="/register"
                                            className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#FF8C00] hover:bg-[#FFD580] md:py-4 md:text-lg md:px-10"
                                        >
                                            Get started
                                        </Link>
                                    </div>
                                    <div className="mt-3 sm:mt-0 sm:ml-3">
                                        <a
                                            href="mailto:info@tutoringforall.org"
                                            className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-gray-700 bg-[#FFD580] hover:bg-[#FF8C00] md:py-4 md:text-lg md:px-10"
                                        >
                                            Contact us
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
                <div className="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                    <img
                        className="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                        src={Example}
                        alt="Hero example"
                    />
                </div>
            </div>
            <div className="py-12 bg-[#DBEAFE]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="lg:text-center">
                        <h2 className="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                            Better Scheduling
                        </h2>
                    </div>
                    <div className="mt-10">
                        <dl className="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                            {features.map(feature => (
                                <div key={feature.name} className="relative">
                                    <dt>
                                        <div className="absolute flex items-center justify-center h-12 w-12 rounded-md bg-gray-800 text-white">
                                            <feature.icon
                                                className="h-6 w-6"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <p className="ml-16 text-lg leading-6 font-large font-extrabold text-gray-900">
                                            {feature.name}
                                        </p>
                                    </dt>
                                    <dd className="mt-2 ml-16 text-base text-gray">
                                        {feature.description}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </div>
                </div>
            </div>
            <div className="bg-[#D1FAE5]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div className="lg:text-center">
                        <h2 className="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                            Our Mission
                        </h2>
                    </div>
                    <div className="mt-10 mx-auto text-center space-y-6 text-xl sm:text-2xl leading-6 font-medium text-gray-900 lg:w-2/3">
                        <p>
                            The Digital Literacy team aims to bring educators
                            to speakers and influencers. With the RFA Scheduler, 
                            teachers are able to bring their classes
                            to educational events hosted by individuals from 
                            different communites.  
                        </p>
                        <p>
                            We are a part of a larger organization,{' '}
                            <a
                                className="link-inline"
                                href="https://roboticsforall.net"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Robotics for All
                            </a>
                            , a 501 (c)(3) nonprofit organization that provides
                            free STEM education to students of all backgrounds
                            across the nation.
                        </p>
                        <p>
                            The Digital Literacy team is made up of passionate 
                            and determined individuals who want to bring quality 
                            education to students from all communites{' '}
                            . If you would like to donate, please{' '}
                            <a
                                className="link-inline"
                                href="https://www.paypal.com/donate?hosted_button_id=9TFKKWS9M78ZS"
                                target="_blank"
                                rel="noreferrer"
                            >
                                click here
                            </a>
                            .
                        </p>
                    </div>
                </div>
                <Footer />
            </div>
        </>
    )
}

export default Welcome
