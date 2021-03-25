import { Inertia } from '@inertiajs/inertia'
import { InertiaLink, usePage } from '@inertiajs/inertia-react'
import React, { useState } from 'react'
import AuthLayout from '../../Layouts/AuthLayout'
import Logo from '../../Components/Logo'
import { DateTime } from 'luxon'

const Register = () => {
    const { errors } = usePage().props
    const [values, setValues] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        code: '',
        timezone: DateTime.local().zoneName
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
        Inertia.post(route('register'), values)
    }
    return (
        <AuthLayout>
            <div className='flex flex-nowrap justify-center mb-6'>
                <Logo />
                <span className='ml-3 text-xl p-3 bg-gray-300 rounded-md'>Register</span>
            </div>
            <form onSubmit={handleSubmit} noValidate>
                <div className='mb-3'>
                    <input className='form-field' name='name' type='text' placeholder='Full name' value={values.name} onChange={handleChange}></input>
                    {errors.name && <div className='form-error'>{errors.name}</div>}
                </div>
                <div className='mb-3'>
                    <input className='form-field' name='email' type='email' placeholder='Email' value={values.email} onChange={handleChange}></input>
                    {errors.email && <div className='form-error'>{errors.email}</div>}
                </div>
                <div className='mb-3'>
                    <input className='form-field' name='password' type='password' placeholder='Password' value={values.password} onChange={handleChange}></input>
                    {errors.password && <div className='form-error'>{errors.password}</div>}
                </div>
                <div className='mb-3'>
                    <input className='form-field' name='password_confirmation' type='password' placeholder='Confirm password' value={values.password_confirmation} onChange={handleChange}></input>
                    {errors.password_confirmation && <div className='form-error'>{errors.password_confirmation}</div>}
                </div>
                <div className='mb-6'>
                    <input className='form-field' name='code' type='text' placeholder='Account code' value={values.code} onChange={handleChange}></input>
                    {errors.code && <div className='form-error'>{errors.code}</div>}
                </div>
                <button className='btn-positive w-full h-12' type='submit'>Register</button>
            </form>
            <p className='text-center mt-3'>Aready have an account? <InertiaLink className='link-inline' href='/login'>Login here.</InertiaLink></p>
        </AuthLayout>
    )
}

export default Register
