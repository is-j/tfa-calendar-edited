import { Children } from 'react'
import { Head, usePage, Link } from '@inertiajs/inertia-react'
import { CogIcon } from '@heroicons/react/outline'
import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import Main from '../components/Main'
import SubjectsModal from '../components/modals/SubjectsModal'
import LanguagesModal from '../components/modals/LanguagesModal'

const Settings = () => {
    const { user } = usePage().props
    return (
        <AppLayout>
            <Head>
                <title>Settings &middot; Tutoring for All Calendar</title>
                <meta name="author" content="Dennis Eum"></meta>
                <meta name="robots" content="none"></meta>
            </Head>
            <Header>
                <Header.Title>Settings</Header.Title>
                <Header.Icon>
                    <CogIcon />
                </Header.Icon>
            </Header>
            <Main>
                <ol>
                    <InfoRow>
                        <InfoRow.Label>Name</InfoRow.Label>
                        <InfoRow.Content>{user.name}</InfoRow.Content>
                    </InfoRow>
                    <InfoRow>
                        <InfoRow.Label>Type</InfoRow.Label>
                        <InfoRow.Content className="capitalize">
                            {user.role_name}
                        </InfoRow.Content>
                    </InfoRow>
                    <InfoRow>
                        <InfoRow.Label>Email</InfoRow.Label>
                        <InfoRow.Content>{user.email}</InfoRow.Content>
                    </InfoRow>
                    <InfoRow>
                        <InfoRow.Label>Password</InfoRow.Label>
                        <InfoRow.Content>
                            <Link
                                className="uppercase h-8 px-2 rounded bg-gray-200 hover:bg-gray-300"
                                href="/update-password"
                                as="button"
                                type="button"
                            >
                                Update
                            </Link>
                        </InfoRow.Content>
                    </InfoRow>
                    {user.role_name === 'speaker' && (
                        <>
                            <InfoRow>
                                <InfoRow.Label>Modules</InfoRow.Label>
                                <InfoRow.Content>
                                    <SubjectsModal />
                                </InfoRow.Content>
                            </InfoRow>
                            <InfoRow>
                                <InfoRow.Label>Languages</InfoRow.Label>
                                <InfoRow.Content>
                                    <LanguagesModal />
                                </InfoRow.Content>
                            </InfoRow>
                        </>
                    )}
                </ol>
            </Main>
        </AppLayout>
    )
}

const InfoRow = props => (
    <>
        <li className="sm:flex items-center border-b border-gray-300 py-3">
            <div className="sm:w-32">
                <div className="text-gray-400 uppercase text-sm">
                    {Children.map(props.children, child => {
                        if (child.type.displayName === 'Label') return child
                    })}
                </div>
            </div>
            {Children.map(props.children, child => {
                if (child.type.displayName === 'Content')
                    return (
                        <>
                            <div
                                className={`text-lg ${
                                    child.props.className
                                        ? child.props.className
                                        : ''
                                }`}
                            >
                                {child}
                            </div>
                        </>
                    )
            })}
        </li>
    </>
)

const Label = ({ children }) => children
Label.displayName = 'Label'
InfoRow.Label = Label

const Content = ({ children }) => children
Content.displayName = 'Content'
InfoRow.Content = Content

export default Settings
