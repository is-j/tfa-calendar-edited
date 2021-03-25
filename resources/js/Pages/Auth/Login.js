import { Inertia } from '@inertiajs/inertia'
import { InertiaLink, usePage } from '@inertiajs/inertia-react'
import React, { useState } from 'react'
import Logo from '../../Components/Logo'
import AuthLayout from '../../Layouts/AuthLayout'

const Login = () => {
    const { errors } = usePage().props
    const [values, setValues] = useState({
        email: '',
        password: ''
    })
    const handleChange = (e) => {
        const key = e.target.name
        const value = e.target.value
        setValues(values => ({
            ...values,
            [key]: value,
        }));
    }
    const handleSubmit = (e) => {
        e.preventDefault()
        Inertia.post(route('login'), values)
    }
    return (
        <AuthLayout>
            <div className='flex flex-nowrap justify-center mb-6'>
                <Logo />
                <span className='ml-3 text-xl p-3 bg-gray-300 rounded-md'>Login</span>
            </div>
            <form onSubmit={handleSubmit} noValidate>
                <div className='mb-3'>
                    <input className='form-field' name='email' type='email' placeholder='Email' value={values.email} onChange={handleChange}></input>
                    {errors.email && <div className='form-error'>{errors.email}</div>}
                </div>
                <div className='mb-6'>
                    <input className='form-field' name='password' type='password' placeholder='Password' value={values.password} onChange={handleChange}></input>
                    {errors.password && <div className='form-error'>{errors.password}</div>}
                </div>
                <button className='btn-positive w-full h-12' type='submit'>Login</button>
            </form>
            <p className='text-center mt-3'>Don't have an account? <InertiaLink className='link-inline' href='/register'>Register here.</InertiaLink></p>
        </AuthLayout>
    )
}

export default Login
