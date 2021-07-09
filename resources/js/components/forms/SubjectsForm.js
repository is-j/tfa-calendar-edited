import { forwardRef, useState, useRef } from 'react'
import { useForm, usePage } from '@inertiajs/inertia-react'

const SubjectsForm = forwardRef((props, ref) => {
    const { allSubjects, userSubjects } = usePage().props
    const [searchValue, setSearchValue] = useState('')
    const contentRef = useRef(null)
    const { data, setData, put, errors } = useForm(userSubjects)
    const handleChange = e => setData(e.target.name, e.target.checked)
    const handleSubmit = e => {
        e.preventDefault()
        put('/user/subjects', {
            onError: () => {
                contentRef.current.scrollTop = contentRef.current.scrollHeight
            },
            onSuccess: () => (props.onSuccess)()
        })
    }
    return (
        <form ref={ref} onSubmit={handleSubmit} noValidate>
            <div className="px-6">
                <input className="form-field h-12 mb-3" type="text" value={searchValue} onChange={e => setSearchValue(e.target.value)} placeholder="Search subjects..."></input>
            </div>
            <div className="sm:max-h-96 h-[calc(100vh-326px)] overflow-auto" ref={contentRef}>
                <ul className="space-y-4 pb-6">
                    {allSubjects.map(subject => {
                        if (subject.name.toLowerCase().indexOf(searchValue) > -1) {
                            return (
                                <SubjectRow key={subject.id} subject={subject} data={data} onChange={handleChange} />
                            )
                        }
                    })}
                    {errors.subjects && <li className="form-error mt-2 mx-6">{errors.subjects}</li>}
                </ul>
            </div>
        </form>
    )
})

const SubjectRow = (props) => (<>
    <li className="mx-6">
        <label className="hover:cursor-pointer" htmlFor={props.subject.name}>
            <div className="flex justify-between items-center shadow-md py-1 px-3 rounded bg-gray-50">
                <span className="uppercase sm:text-lg">{props.subject.name}</span>
                <div className="props-check">
                    <input name={props.subject.id} id={props.subject.name} type="checkbox" checked={props.data[props.subject.id]} onChange={props.onChange}></input>
                </div>
            </div>
        </label>
    </li>
</>)

export default SubjectsForm
