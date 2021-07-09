import { forwardRef } from 'react'
import { useForm } from '@inertiajs/inertia-react'
import { DateTime } from 'luxon'
import { InformationCircleIcon } from '@heroicons/react/outline'

const ClaimEventForm = forwardRef((props, ref) => {
    const { data, setData, put, errors, hasErrors } = useForm({
        info: '',
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        put(`/events/${props.event.id}`, {
            onSuccess: () => (props.onSuccess)()
        })
    }
    return (
        <form ref={ref} className={hasErrors ? 'form-validate' : ''} onSubmit={handleSubmit} noValidate>
            <div className="mb-3">
                <span className="font-bold text-xl">{DateTime.fromISO(props.event.start).toFormat('DDD')}</span>
            </div>
            <div className="text-left">
                <div className="flex items-center space-x-2 mb-4">
                    <InformationCircleIcon className="h-7 w-7" />
                    <span>{`${props.event.tutor_name} is tutoring ${props.event.subject_name}`}</span>
                </div>
                <div className="form-floating mb-3">
                    <textarea className={`form-field ${errors.info ? 'invalid' : ''}`} name="info" value={data.info} onChange={handleChange} placeholder="What do you need help with?"></textarea>
                    <label>What do you need help with?</label>
                    {errors.info && <div className="form-error">{errors.info}</div>}
                </div>
            </div>
        </form>
    )
})

export default ClaimEventForm
