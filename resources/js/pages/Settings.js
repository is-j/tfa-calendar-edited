import AppLayout from '../layouts/AppLayout'
import Header from '../components/Header'
import { CogIcon } from '@heroicons/react/outline'

const Settings = () => {
    return (
        <AppLayout>
            <Header>
                <Header.Title>Settings</Header.Title>
                <Header.Icon><CogIcon /></Header.Icon>
            </Header>
        </AppLayout>
    )
}

export default Settings