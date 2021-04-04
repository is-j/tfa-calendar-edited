import { usePage } from '@inertiajs/inertia-react'
import React from 'react'
import Logo from '../../Components/Logo'
import AuthLayout from '../../Layouts/AuthLayout'

const Setup = () => {
    const { user, subjects } = usePage().props
    const { data, setData, post, processing, errors } = useForm(user.role_name === 'tutor' ? {
        meeting_link: '',
        bio: '',
        subject_id: '1'
    } : {
        terms: false
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        post(route('setup'))
    }
    return (
        <AuthLayout>
            <div className='flex flex-nowrap justify-center mb-6'>
                <Logo />
                <span className='ml-3 text-xl p-3 bg-gray-300 rounded-md'>Setup</span>
            </div>
            <form onSubmit={handleSubmit} noValidate>
                {user.role_name === 'tutor' ?
                    <>
                        <div className='mb-3 form-floating'>
                            <input className='form-field' name='meeting_link' type='text' placeholder='Meeting link' value={data.meeting_link} onChange={handleChange}></input>
                            <label>Meeting link</label>
                            <div className='form-help'>E.g. Zoom or Google Meet</div>
                            {errors.meeting_link && <div className='form-error'>{errors.meeting_link}</div>}
                        </div>
                        <div className='mb-3 form-floating'>
                            <textarea className='form-field form-textarea' name='bio' value={data.bio} onChange={handleChange} placeholder='Introduce yourself'></textarea>
                            <label>Introduce yourself</label>
                            <div className='form-help'>Max 1000 characters</div>
                            {errors.bio && <div className='form-error'>{errors.bio}</div>}
                        </div>
                        <div className='mb-6 form-floating'>
                            <select className='form-field form-select' name='subject_id' value={data.subject_id} onChange={handleChange}>
                                {subjects.map(subject => <option key={subject.id} value={subject.id}>{subject.name}</option>)}
                            </select>
                            <label>Subject</label>
                            <div className='form-help'>You can select more later</div>
                        </div>
                    </> :
                    <>
                        <div className='mb-3'>
                            <p className='px-3'>
                                By creating this account, I understand that I miss 3 appointments without 2 hour notice in advance, I will be put under a probation period of 7 days from signing up. I can negotiate with my tutor to schedule for a long term tutoring service, which constitutes of 6 sessions maximum.
                            </p>
                        </div>
                        <div className='mb-6 pl-5'>
                            <div className='form-check'>
                                <input name='terms' id='terms' type='checkbox' checked={data.terms} onChange={handleChange}></input>
                                <label htmlFor='terms'>I agree to these terms</label>
                            </div>
                            {errors.terms && <div className='form-error'>{errors.terms}</div>}
                        </div>
                    </>
                }
                <button className='btn-positive w-full h-12' type='submit' disabled={processing}>Submit</button>
            </form>
        </AuthLayout>
    )
}

export default Setup
