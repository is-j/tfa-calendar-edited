import React, { useEffect, useRef, useState } from 'react'
import { CSSTransition } from 'react-transition-group'

const ModalItem = ({ onClose, label, children, type }) => {
    const selfRef = useRef(null)
    const [showSelf, setShowSelf] = useState(false)
    useEffect(() => {
        setShowSelf(true)
    }, [])
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (showSelf && selfRef.current && !selfRef.current.contains(e.target)) {
                setShowSelf(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => {
            document.removeEventListener('mousedown', handleClickOutside)
        }
    }, [selfRef, showSelf])
    return (
        <CSSTransition in={showSelf} timeout={300} classNames='transition-all' unmountOnExit onExit={() => (onClose)()}>
            <div className='inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full' role='dialog' aria-modal='true' aria-labelledby='modal-headline' ref={selfRef}>
                <div className='bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4'>
                    <div className='sm:flex sm:items-start'>
                        <div className={`mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10 ${type === 'positive' ? 'bg-blue-100' : 'bg-red-100'}`}>
                            <svg className={`h-6 w-6 ${type === 'positive' ? 'text-blue-600' : 'text-red-600'}`} xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'>
                                {children[0]}
                            </svg>
                        </div>
                        <div className='mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left'>
                            <h3 className='text-lg leading-6 font-medium text-gray-900' id='modal-headline'>{label}</h3>
                            <div className='mt-2'>
                                {children[1]}
                            </div>
                        </div>
                    </div>
                </div>
                <div className='bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse'>
                    {children[2]}
                    <button type='button' className='btn-modal btn-neutral mt-2 sm:mt-0 sm:mr-3' onClick={() => setShowSelf(false)}>Cancel</button>
                </div>
            </div>
        </CSSTransition>
    )
}

export default ModalItem
