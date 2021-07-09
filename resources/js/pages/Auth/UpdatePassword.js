import { Inertia } from '@inertiajs/inertia'
import { InertiaLink, useForm } from '@inertiajs/inertia-react'
import AuthLayout from '../../layouts/AuthLayout'

const UpdatePassword = () => {
    const { data, setData, put, processing, errors, hasErrors, reset } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        put('/user/password', {
            onError: () => reset(),
            onSuccess: () => Inertia.visit('/settings', { method: 'get' })
        })
    }
    return (
        <AuthLayout>
            <AuthLayout.Title>Update Password</AuthLayout.Title>
            <AuthLayout.Content>
                <form className={hasErrors ? 'form-validate' : ''} onSubmit={handleSubmit} noValidate>
                    <div className="mb-3">
                        <input className={`form-field h-12 ${errors.updatePassword?.current_password ? 'invalid' : ''}`} name="current_password" type="password" placeholder="Current Password" value={data.current_password} onChange={handleChange} autoComplete="current-password"></input>
                        {errors.updatePassword?.current_password && <div className="form-error">{errors.updatePassword?.current_password}</div>}
                    </div>
                    <div className="mb-3">
                        <input className={`form-field h-12 ${errors.updatePassword?.password ? 'invalid' : ''}`} name="password" type="password" placeholder="New Password" value={data.password} onChange={handleChange} autoComplete="new-password"></input>
                        {errors.updatePassword?.password && <div className="form-error">{errors.updatePassword?.password}</div>}
                    </div>
                    <div className="mb-6">
                        <input className={`form-field h-12 ${errors.updatePassword?.password_confirmation ? 'invalid' : ''}`} name="password_confirmation" type="password" placeholder="Confirm Password" value={data.password_confirmation} onChange={handleChange} autoComplete="new-password"></input>
                        {errors.updatePassword?.password_confirmation && <div className="form-error">{errors.updatePassword?.password_confirmation}</div>}
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 sm:grid-flow-row-dense gap-3">
                        <button className="btn-positive w-full h-12" type="submit" disabled={processing}>Update</button>
                        <InertiaLink className="btn-neutral w-full h-12 sm:order-first" href="/settings" as="button" type="button">Back</InertiaLink>
                    </div>
                </form>
            </AuthLayout.Content>
        </AuthLayout>
    )
}

export default UpdatePassword
