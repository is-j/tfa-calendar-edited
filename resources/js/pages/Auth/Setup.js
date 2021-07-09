import { usePage, useForm } from '@inertiajs/inertia-react'
import AuthLayout from '../../layouts/AuthLayout'

const Setup = () => {
    const { user, allSubjects } = usePage().props
    const { data, setData, post, processing, errors, hasErrors } = useForm(user.role_name === 'tutor' ? {
        meeting_link: '',
        bio: '',
        subject_id: '1',
    } : {
        terms: false,
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        post('/setup')
    }
    return (
        <AuthLayout>
            <AuthLayout.Title>Setup</AuthLayout.Title>
            <AuthLayout.Content>
                <form className={hasErrors ? 'form-validate' : ''} onSubmit={handleSubmit} noValidate>
                    {user.role_name === 'tutor' && (
                        <>
                            <div className="mb-3 form-floating">
                                <input className={`form-field h-12 ${errors.meeting_link ? 'invalid' : ''}`} name="meeting_link" type="text" placeholder="Meeting link" value={data.meeting_link} onChange={handleChange}></input>
                                <label>Meeting link</label>
                                <div className="form-help">E.g. Zoom or Google Meet</div>
                                {errors.meeting_link && <div className="form-error">{errors.meeting_link}</div>}
                            </div>
                            <div className="mb-3 form-floating">
                                <textarea className={`form-field ${errors.bio ? 'invalid' : ''}`} name="bio" value={data.bio} onChange={handleChange} placeholder="Introduce yourself"></textarea>
                                <label>Introduce yourself</label>
                                <div className="form-help">Max 1000 characters</div>
                                {errors.bio && <div className="form-error">{errors.bio}</div>}
                            </div>
                            <div className="mb-6 form-floating">
                                <select className={`form-field h-12 ${errors.subject_id ? 'invalid' : ''}`} name="subject_id" value={data.subject_id} onChange={handleChange}>
                                    {allSubjects.map(subject => <option key={subject.id} value={subject.id}>{subject.name}</option>)}
                                </select>
                                <label>Subject</label>
                                <div className="form-help">You can select more later</div>
                            </div>
                        </>
                    )}
                    {user.role_name === 'student' && (
                        <>
                            <div className="mb-3">
                                <p className="px-3">
                                    By creating this account, I understand that I miss 3 appointments without 2 hour notice in advance, I will be put under a probation period of 7 days from signing up. I can negotiate with my tutor to schedule for a long term tutoring service, which constitutes of 6 sessions maximum.
                                </p>
                            </div>
                            <div className="mb-6 pl-3">
                                <div className="form-check">
                                    <input name="terms" id="terms" type="checkbox" checked={data.terms} onChange={e => setData(e.target.name, e.target.checked)}></input>
                                    <label htmlFor="terms">I agree to these terms</label>
                                </div>
                                {errors.terms && <div className="form-error">{errors.terms}</div>}
                            </div>
                        </>
                    )}
                    <button className="btn-positive w-full h-12" type="submit" disabled={processing}>Submit</button>
                </form>
            </AuthLayout.Content>
        </AuthLayout>
    )
}

export default Setup
