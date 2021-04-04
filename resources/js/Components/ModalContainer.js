import { useForm } from '@inertiajs/inertia-react'
import React, { Children, useEffect, useRef, useState } from 'react'
import { CSSTransition } from 'react-transition-group'
import ModalItem from './ModalItem'
import { DateTime } from 'luxon'

const ModalContainer = ({ currentModalName, onClose, info }) => {
    if (currentModalName !== '' && info !== {}) {
        const [showBackground, setShowBackground] = useState(false)
        const { data, setData, post, errors, transform, clearErrors } = useForm({
            start: ''
        })
        const createSlotForm = useRef(null)
        useEffect(() => {
            setData('start', DateTime.fromISO(info.dateStr).toFormat("yyyy-MM-dd'T'HH:mm"))
            setShowBackground(true)
        }, [])
        const handleChange = e => setData(e.target.name, e.target.value)
        const handleSubmit = e => {
            e.preventDefault()
            transform(data => ({
                ...data,
                start: DateTime.fromFormat(data.start, "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm')
            }))
            post('/api/slot/create')
        }
        const handleExit = () => {
            clearErrors()
            (onClose)()
        }
        const children = [
            <ModalItem name='createSlotModal' label='Create slot' type='positive' onClose={() => setShowBackground(false)}>
                <>
                    <path strokeLinecap='round' strokeLinejoin='round' strokeWidth={2} d='M12 6v6m0 0v6m0-6h6m-6 0H6' />
                </>
                <>
                    <form ref={createSlotForm} onSubmit={handleSubmit} noValidate>
                        <div className='mb-3'>
                            <input className='form-field' name='start' type='datetime-local' value={data.start} onChange={handleChange} required></input>
                            {errors.start && <div className='form-error'>{errors.start}</div>}
                        </div>
                    </form>
                </>
                <>
                <button type='button' className='btn-negative' onClick={clearErrors}>asdfasd</button>
                    <button type='button' className='btn-modal btn-positive' onClick={() => createSlotForm.current.requestSubmit()}>Create</button>
                </>
            </ModalItem>,
            <ModalItem name='testModal2' onClose={() => setShowBackground(false)} />
        ]
        return (
            <div className='fixed z-10 inset-0 overflow-y-auto'>
                <div className='flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0'>
                    <CSSTransition in={showBackground} timeout={300} classNames='transition-opacity' unmountOnExit onExited={handleExit}>
                        <div className='fixed inset-0' aria-hidden='true'>
                            <div className='absolute inset-0 bg-gray-500 opacity-75'></div>
                        </div>
                    </CSSTransition>
                    <span className='hidden sm:inline-block sm:align-middle sm:h-screen' aria-hidden='true'>&#8203;</span>
                    {Children.map(children, (child) => {
                        if (child.props.name === currentModalName) {
                            return child
                        }
                    })}
                </div>
            </div>
        )
    }
    return null
}

export default ModalContainer
