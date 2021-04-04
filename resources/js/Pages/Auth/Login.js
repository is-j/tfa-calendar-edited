import { InertiaLink, useForm } from '@inertiajs/inertia-react'
import React from 'react'
import Logo from '../../Components/Logo'
import AuthLayout from '../../Layouts/AuthLayout'

const Login = () => {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: ''
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        post(route('login'), {
            onError: () => setData('password', '')
        })
    }
    return (
        <AuthLayout>
            <div className='flex flex-nowrap justify-center mb-6'>
                <Logo />
                <span className='ml-3 text-xl p-3 bg-gray-300 rounded-md'>Login</span>
            </div>
            <form onSubmit={handleSubmit} noValidate>
                <div className='mb-3'>
                    <input className='form-field' name='email' type='email' placeholder='Email' value={data.email} onChange={handleChange}></input>
                    {errors.email && <div className='form-error'>{errors.email}</div>}
                </div>
                <div className='mb-6'>
                    <input className='form-field' name='password' type='password' placeholder='Password' value={data.password} onChange={handleChange}></input>
                    {errors.password && <div className='form-error'>{errors.password}</div>}
                </div>
                <button className='btn-positive w-full h-12' type='submit' disabled={processing}>Login</button>
            </form>
            <p className='text-center mt-3'>Don't have an account? <InertiaLink className='link-inline' href='/register'>Register here.</InertiaLink></p>
        </AuthLayout>
    )
}

export default Login
