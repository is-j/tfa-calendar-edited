import { forwardRef } from 'react'
import { useForm } from '@inertiajs/inertia-react'
import { DateTime } from 'luxon'
import { InformationCircleIcon, AtSymbolIcon, VideoCameraIcon } from '@heroicons/react/outline'

const ViewSlotForm = forwardRef((props, ref) => {
    const { data, setData, post, errors } = useForm({
        slot_id: props.info.event.id,
        repeat: false,
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        post('/access/slot/cancel')
    }
    return (
        <form ref={ref} onSubmit={handleSubmit} noValidate>
            <div className="mb-3">
                <span className="font-bold text-xl">{DateTime.fromISO(props.info.event.startStr).toFormat('DDD')} ({props.info.event.extendedProps.claimed ? '' : 'Unclaimed'})</span>
            </div>
            {props.info.event.extendedProps.claimed ? <>
                <div className="flex items-center space-x-2 mb-2">
                    <InformationCircleIcon className="h-7 w-7" />
                    <div><span name="student_name"></span><span>&nbsp;is learning&nbsp;</span></div><span name="subject_name"></span>
                </div>
                <div className="flex items-center space-x-2 mb-4">
                    <AtSymbolIcon className="h-7 w-7" />
                    <span name="student_email"></span>
                </div>
                <div className="form-floating mb-3">
                    <textarea className="form-field" name="info" disabled></textarea>
                    <label>What do they need help with?</label>
                </div>
                <div>
                    <a className="btn-positive flex justify-center items-center space-x-2 text-base tracking-wide w-full h-12" href="#" target="_blank" rel="noreferrer" name="meeting_link">
                        <VideoCameraIcon className="h-7 w-7" />
                        <span>Meeting link</span>
                    </a>
                </div>
            </> : <></>}
            <div>
                <div className="form-check">
                    <input name="repeat" id="repeat" type="checkbox" checked={data.repeat} onChange={e => setData(e.target.name, e.target.checked)}></input>
                    <label htmlFor="repeat">Delete all following slots of the same day of week and time</label>
                </div>
                {errors.repeat && <div className="form-error">{errors.repeat}</div>}
            </div>
            <input type="hidden" value={data.slot_id}></input>
        </form>
    )
})

export default ViewSlotForm
