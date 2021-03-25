import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import React, { useEffect, useRef, useState } from 'react'
import AppLayout from '../Layouts/AppLayout'
import ModalContainer from '../Components/ModalContainer'
import ModalItem from '../Components/ModalItem'

const Calendar = () => {
    const mobileToolbarState = {
        left: 'title',
        right: 'prev,next'
    }
    const desktopToolbarState = {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    }
    const calendarRef = useRef(null)
    const [toolbarState, setToolbarState] = useState(window.innerWidth > 768 ? desktopToolbarState : mobileToolbarState)
    const [modalName, setModalName] = useState('')
    useEffect(() => {
        let prevWidth
        const checkWidth = () => {
            const currentWidth = window.innerWidth
            if ((currentWidth <= 768 && prevWidth >= 768) || (currentWidth <= 768 && prevWidth == null)) {
                calendarRef.current.getApi().changeView('timeGridDay')
                setToolbarState(mobileToolbarState)
            } else if (currentWidth >= 768 && prevWidth <= 768) {
                calendarRef.current.getApi().changeView('dayGridMonth')
                setToolbarState(desktopToolbarState)
            }
            prevWidth = window.innerWidth
        }
        window.addEventListener('resize', checkWidth)
        return () => {
            window.removeEventListener('resize', checkWidth)
        }
    }, [window])
    return (
        <AppLayout>
            <div className='max-w-7xl mx-auto pb-6 px-3 sm:px-6 lg:px-8 h-adjust md:h-full'>
                <ModalContainer currentModalName={modalName}>
                    <ModalItem name='testModal' />
                </ModalContainer>
                <FullCalendar
                    ref={calendarRef}
                    plugins={[dayGridPlugin, timeGridPlugin]}
                    initialView={window.innerWidth > 768 ? 'dayGridMonth' : 'timeGridDay'}
                    headerToolbar={toolbarState}
                    timeZone='local'
                    selectable={true}
                    nowIndicator={true}
                    lazyFetching={true}
                    events={[
                        { title: 'event 1', date: '2019-04-01' },
                        { title: 'event 2', date: '2019-04-02' }
                    ]}
                />
            </div>
        </AppLayout>
    )
}

export default Calendar
