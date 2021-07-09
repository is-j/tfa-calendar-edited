import { InertiaLink, useForm } from '@inertiajs/inertia-react'
import AuthLayout from '../../layouts/AuthLayout'
import { DateTime } from 'luxon'

const Register = () => {
    const { data, setData, post, processing, errors, hasErrors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        code: '',
        timezone: DateTime.local().zoneName,
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        post('/register', {
            onError: () => setData('password_confirmation', '')
        })
    }
    return (
        <AuthLayout>
            <AuthLayout.Title>Register</AuthLayout.Title>
            <AuthLayout.Content>
                <form className={hasErrors ? 'form-validate' : ''} onSubmit={handleSubmit} noValidate>
                    <div className="mb-3">
                        <input className={`form-field h-12 ${errors.name ? 'invalid' : ''}`} name="name" type="text" placeholder="Full name" value={data.name} onChange={handleChange}></input>
                        {errors.name && <div className="form-error">{errors.name}</div>}
                    </div>
                    <div className="mb-3">
                        <input className={`form-field h-12 ${errors.email ? 'invalid' : ''}`} name="email" type="email" placeholder="Email" value={data.email} onChange={handleChange}></input>
                        {errors.email && <div className="form-error">{errors.email}</div>}
                    </div>
                    <div className="mb-3">
                        <input className={`form-field h-12 ${errors.password ? 'invalid' : ''}`} name="password" type="password" placeholder="Password" value={data.password} onChange={handleChange}></input>
                        {errors.password && <div className="form-error">{errors.password}</div>}
                    </div>
                    <div className="mb-3">
                        <input className={`form-field h-12 ${errors.password_confirmation ? 'invalid' : ''}`} name="password_confirmation" type="password" placeholder="Confirm password" value={data.password_confirmation} onChange={handleChange}></input>
                        {errors.password_confirmation && <div className="form-error">{errors.password_confirmation}</div>}
                    </div>
                    <div className="mb-6">
                        <input className={`form-field h-12 ${errors.code ? 'invalid' : ''}`} name="code" type="text" placeholder="Account code" value={data.code} onChange={handleChange}></input>
                        {errors.code && <div className="form-error">{errors.code}</div>}
                    </div>
                    <button className="btn-positive w-full h-12" type="submit" disabled={processing}>Register</button>
                </form>
                <p className="text-center mt-3">Aready have an account? <InertiaLink className="link-inline" href="/login">Login here.</InertiaLink></p>
            </AuthLayout.Content>
        </AuthLayout>
    )
}

export default Register
