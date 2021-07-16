import { forwardRef } from 'react'
import { useForm } from '@inertiajs/inertia-react'
import { DateTime } from 'luxon'
import { InformationCircleIcon, AtSymbolIcon, VideoCameraIcon } from '@heroicons/react/outline'

const ViewEventForm = forwardRef((props, ref) => {
    const { data, setData, post, errors } = useForm(props.event.student_name ? {
        event: props.event,
    } : {
        repeat: false,
    })
    const handleSubmit = e => {
        e.preventDefault()
        if (props.event.student_name) {
            post('/cancel')
        } else {
            post(`/events/${props.event.id}`, {
                onSuccess: () => (props.onSuccess)()
            })
        }

    }
    return (
        <form ref={ref} onSubmit={handleSubmit} noValidate>
            <div className="mb-3">
                <span className="font-bold text-xl">{DateTime.fromISO(props.event.start).toFormat('ff')} {props.event.student_name ? '' : '(Unclaimed)'}</span>
            </div>
            <div className="text-left">
                {props.event.student_name ? (<>
                    <div className="flex items-center space-x-2 mb-2">
                        <InformationCircleIcon className="h-7 w-7" />
                        <span>{`Tutoring ${props.event.subject_name} to ${props.event.student_name}`}</span>
                    </div>
                    <div className="flex items-center space-x-2 mb-4">
                        <AtSymbolIcon className="h-7 w-7" />
                        <span>{props.event.student_email}</span>
                    </div>
                    <div className="form-floating mb-3">
                        <textarea className="form-field !border-0 !bg-gray-200" value={props.event.info} disabled></textarea>
                        <label>What do they need help with?</label>
                    </div>
                    <div>
                        <a className="btn-positive flex justify-center items-center space-x-2 text-base tracking-wide w-full h-12" href={props.event.meeting_link} target="_blank" rel="noreferrer">
                            <VideoCameraIcon className="h-7 w-7" />
                            <span>Meeting link</span>
                        </a>
                    </div>
                </>) : (<>
                    <div className="flex items-center space-x-2 mb-3">
                        <InformationCircleIcon className="h-7 w-7" />
                        <span>{props.event.subject_name}</span>
                    </div>
                    <div>
                        <div className="form-check">
                            <input name="repeat" id="repeat" type="checkbox" checked={data.repeat} onChange={e => setData(e.target.name, e.target.checked)}></input>
                            <label htmlFor="repeat">Delete all following events of the same day and time</label>
                        </div>
                        {errors.repeat && <div className="form-error">{errors.repeat}</div>}
                    </div>
                </>)}
            </div>
        </form>
    )
})

export default ViewEventForm
