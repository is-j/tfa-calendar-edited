import { forwardRef } from 'react'
import { useForm, usePage } from '@inertiajs/inertia-react'
import { DateTime } from 'luxon'

const CreateSlotForm = forwardRef((props, ref) => {
    const { subjects } = usePage().props
    const { data, setData, post, errors, hasErrors, transform } = useForm({
        start: DateTime.fromISO(props.info.dateStr).toFormat("yyyy-MM-dd'T'HH:mm"),
        subject_id: '1',
        repeat: false,
    })
    const handleChange = e => setData(e.target.name, e.target.value)
    const handleSubmit = e => {
        e.preventDefault()
        transform(data => ({
            ...data,
            start: DateTime.fromFormat(data.start, "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm'),
        }))
        post('/api/slot/create', {
            onFinish: () => (props.onSuccess)()
        })
    }
    return (
        <form ref={ref} className={hasErrors ? 'form-validate' : ''} onSubmit={handleSubmit} noValidate>
            <div className="mb-3">
                <input className={`form-field h-12 ${errors.email ? 'invalid' : ''}`} name="start" type="datetime-local" value={data.start} onChange={handleChange}></input>
                {errors.start && <div className="form-error">{errors.start}</div>}
            </div>
            <div className="mb-3 form-floating">
                <select className={`form-field h-12 ${errors.subject_id ? 'invalid' : ''}`} name="subject_id" value={data.subject_id} onChange={handleChange}>
                    {subjects.map(subject => <option key={subject.id} value={subject.id}>{subject.name}</option>)}
                </select>
                <label>Subject</label>
            </div>
            <div>
                <div className="form-check">
                    <input name="repeat" id="repeat" type="checkbox" checked={data.repeat} onChange={e => setData(e.target.name, e.target.checked)}></input>
                    <label htmlFor="repeat">Repeat this slot on this day of the week at this time for the next 20 weeks.</label>
                </div>
                {errors.repeat && <div className="form-error">{errors.repeat}</div>}
            </div>
        </form>
    )
})

export default CreateSlotForm
