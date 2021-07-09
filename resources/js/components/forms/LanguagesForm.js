import { forwardRef, useState, useRef } from 'react'
import { useForm, usePage } from '@inertiajs/inertia-react'

const LanguagesForm = forwardRef((props, ref) => {
    const { allLanguages, userLanguages } = usePage().props
    const [searchValue, setSearchValue] = useState('')
    const contentRef = useRef(null)
    const { data, setData, put, errors } = useForm(userLanguages)
    const handleChange = e => setData(e.target.name, e.target.checked)
    const handleSubmit = e => {
        e.preventDefault()
        put('/user/languages', {
            onError: () => {
                contentRef.current.scrollTop = contentRef.current.scrollHeight
            },
            onSuccess: () => (props.onSuccess)()
        })
    }
    return (
        <form ref={ref} onSubmit={handleSubmit} noValidate>
            <div className="px-6">
                <input className="form-field h-12 mb-3" type="text" value={searchValue} onChange={e => setSearchValue(e.target.value)} placeholder="Search languages..."></input>
            </div>
            <div className="sm:max-h-96 h-[calc(100vh-326px)] overflow-auto" ref={contentRef}>
                <ul className="space-y-4 pb-6">
                    {allLanguages.map(language => {
                        if (language.name.toLowerCase().indexOf(searchValue) > -1) {
                            return (
                                <LanguageRow key={language.id} language={language} data={data} onChange={handleChange} />
                            )
                        }
                    })}
                    {errors.languages && <li className="form-error mt-2 mx-6">{errors.languages}</li>}
                </ul>
            </div>
        </form>
    )
})

const LanguageRow = (props) => (<>
    <li className="mx-6">
        <label className="hover:cursor-pointer" htmlFor={props.language.name}>
            <div className="flex justify-between items-center shadow-md py-1 px-3 rounded bg-gray-50">
                <span className="uppercase sm:text-lg">{props.language.name}</span>
                <div className="props-check">
                    <input name={props.language.id} id={props.language.name} type="checkbox" checked={props.data[props.language.id]} onChange={props.onChange}></input>
                </div>
            </div>
        </label>
    </li>
</>)

export default LanguagesForm
