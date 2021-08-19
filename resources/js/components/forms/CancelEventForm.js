import { forwardRef } from 'react'
import { useForm, usePage } from '@inertiajs/inertia-react'
import { DateTime } from 'luxon'
import { InformationCircleIcon, AtSymbolIcon } from '@heroicons/react/outline'

const CancelEventForm = forwardRef((props, ref) => {
    const { user } = usePage().props
    const { data, setData, post, errors, hasErrors } = useForm({
        reason: '',
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        post(`/events/${props.event.id}`)
    }
    return (
        <form
            ref={ref}
            className={hasErrors ? 'form-validate' : ''}
            onSubmit={handleSubmit}
            noValidate
        >
            <div className="mb-3">
                <span className="font-bold text-xl">
                    {DateTime.fromISO(props.event.start).toFormat('DDD')}
                </span>
            </div>
            <div className="text-left">
                <div className="flex items-center space-x-2 mb-2">
                    <InformationCircleIcon className="h-7 w-7" />
                    <span>
                        {user.role_name === 'speaker' &&
                            `Speaking ${props.event.subject_name} to ${props.event.student_name}`}
                        {user.role_name === 'student' &&
                            `Teaching ${props.event.subject_name} from ${props.event.tutor_name}`}
                    </span>
                </div>
                <div className="flex items-center space-x-2 mb-4">
                    <AtSymbolIcon className="h-7 w-7" />
                    <span>
                        {user.role_name === 'speaker' &&
                            props.event.student_email}
                        {user.role_name === 'student' &&
                            props.event.tutor_email}
                    </span>
                </div>
                <div className="form-floating mb-5">
                    <textarea
                        className={`form-field h-12 ${
                            errors.reason ? 'invalid' : ''
                        }`}
                        name="reason"
                        value={data.reason}
                        onChange={handleChange}
                        placeholder="Why are you cancelling?"
                    ></textarea>
                    <label>Why are you cancelling?</label>
                    <div className="form-help">Max 1000 characters</div>
                    {errors.reason && (
                        <div className="form-error">{errors.reason}</div>
                    )}
                </div>
            </div>
        </form>
    )
})

export default CancelEventForm
