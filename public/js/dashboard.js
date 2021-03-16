let subjectId = '0';
let prevWidth = null;
postData('/api/tz/temp', {
    timezone: DateTime.local().zoneName
});
const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    timeZone: 'local',
    initialView: 'dayGridMonth',
    events: (info, successCallback) => {
        fetch(`/api/slot/get/${subjectId}?start=${encodeURIComponent(info.startStr)}&end=${encodeURIComponent(info.endStr)}`, {
            method: 'GET'
        }).then(response => response.json()).then(data => { successCallback(data) });
    },
    selectable: true,
    nowIndicator: true,
    lazyFetching: true,
    loading: isLoading => processingStatus(isLoading),
    dateClick: info => {
        if (accountType == 'tutor') {
            document.getElementById('createSlotForm').querySelector('input[name="start"]').value = DateTime.fromISO(info.dateStr).toFormat("yyyy-MM-dd'T'HH:mm");
            toggleModal('createSlot');
        }
    },
    eventClick: info => {
        if (accountType == 'student') {
            if (info.event.extendedProps.claimed) {
                const unclaimSlotForm = document.getElementById('unclaimSlotForm');
                unclaimSlotForm.querySelector('input[name="start"]').value = DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm");
                matchValue(unclaimSlotForm, info.event.extendedProps, ['span[name="tutor_name"]', 'span[name="tutor_email"]', 'textarea[name="tutor_bio"]', 'span[name="subject_name"]', 'textarea[name="info"]']);
                let temp = '';
                if (info.event.extendedProps.tutor_languages.length == 2) {
                    temp = `${info.event.extendedProps.tutor_languages[0]} and ${info.event.extendedProps.tutor_languages[0]}`;
                } else if (info.event.extendedProps.tutor_languages.length > 1) {
                    for (languageName of info.event.extendedProps.tutor_languages) {
                        if (languageName == info.event.extendedProps.tutor_languages[info.event.extendedProps.tutor_languages.length - 1]) {
                            temp += `and ${languageName}`;
                        } else {
                            temp += `${languageName}, `;
                        }
                    }
                } else {
                    temp = `${info.event.extendedProps.tutor_languages[0]}`;
                }
                unclaimSlotForm.querySelector('span[name="tutor_languages"]').innerText = temp;
                unclaimSlotForm.querySelector('div[name="id"]').setAttribute('data-id', info.event.id);
                unclaimSlotForm.querySelector('div[name="claimed"]').setAttribute('data-claimed', info.event.extendedProps.claimed);
                unclaimSlotForm.querySelector('a[name="meeting_link"]').setAttribute('href', info.event.extendedProps.meeting_link);
                toggleModal('unclaimSlot');
            } else {
                const claimSlotForm = document.getElementById('claimSlotForm');
                claimSlotForm.querySelector('input[name="start"]').value = DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm");
                matchValue(claimSlotForm, info.event.extendedProps, ['span[name="tutor_name"]', 'textarea[name="tutor_bio"]', 'span[name="subject_name"]']);
                let temp = '';
                if (info.event.extendedProps.tutor_languages.length == 2) {
                    temp = `${info.event.extendedProps.tutor_languages[0]} and ${info.event.extendedProps.tutor_languages[0]}`;
                } else if (info.event.extendedProps.tutor_languages.length > 1) {
                    for (languageName of info.event.extendedProps.tutor_languages) {
                        if (languageName == info.event.extendedProps.tutor_languages[info.event.extendedProps.tutor_languages.length - 1]) {
                            temp += `and ${languageName}`;
                        } else {
                            temp += `${languageName}, `;
                        }
                    }
                } else {
                    temp = `${info.event.extendedProps.tutor_languages[0]}`;
                }
                claimSlotForm.querySelector('span[name="tutor_languages"]').innerText = temp;
                claimSlotForm.querySelector('div[name="id"]').setAttribute('data-id', info.event.id);
                claimSlotForm.querySelector('div[name="claimed"]').setAttribute('data-claimed', info.event.extendedProps.claimed);
                toggleModal('claimSlot');
            }
        } else if (accountType == 'tutor') {
            const deleteSlotForm = document.getElementById('deleteSlotForm');
            deleteSlotForm.querySelector('input[name="start"]').value = DateTime.fromISO(info.event.startStr).toFormat("yyyy-MM-dd'T'HH:mm");
            deleteSlotForm.querySelector('span[name="subject_name"]').innerText = info.event.extendedProps.subject_name;
            deleteSlotForm.querySelector('div[name="id"]').setAttribute('data-id', info.event.id);
            deleteSlotForm.querySelector('div[name="claimed"]').setAttribute('data-claimed', info.event.extendedProps.claimed);
            if (info.event.extendedProps.claimed) {
                toggleDisplay(deleteSlotForm, ['input[name="repeat"]'], ['span[name="student_name"]', 'span[name="student_email"]', 'textarea[name="info"]', 'a[name="meeting_link"]']);
                matchValue(deleteSlotForm, info.event.extendedProps, ['span[name="student_name"]', 'span[name="student_email"]', 'textarea[name="info"]']);
                deleteSlotForm.querySelector('a[name="meeting_link"]').setAttribute('href', info.event.extendedProps.meeting_link);
            } else {
                toggleDisplay(deleteSlotForm, ['span[name="student_name"]', 'span[name="student_email"]', 'textarea[name="info"]', 'a[name="meeting_link"]'], ['input[name="repeat"]']);
            }
            toggleModal('deleteSlot');
        }
    }
});
calendar.render();

checkView();
window.addEventListener('resize', () => checkView());

if (accountType == 'tutor') {
    document.getElementById('createSlotForm').addEventListener('submit', function (event) {
        const form = this;
        checkValidity(form, event, () => {
            const start = DateTime.fromFormat(form.querySelector('input[name="start"]').value, "yyyy-MM-dd'T'HH:mm").toUTC().toFormat('yyyy-MM-dd HH:mm');
            const subjectId = form.querySelector('select[name="subject_id"]').value;
            const repeat = form.querySelector('input[name="repeat"]').checked;
            postData('/api/slot/create', {
                start: start,
                subject_id: subjectId,
                repeat: repeat
            }).then(response => {
                toggleModal('createSlot');
                toggleAlert(response);
                calendar.refetchEvents();
                reportInit();
            });
        }, () => {
            if (DateTime.fromFormat(form.querySelector('input[name="start"]').value, "yyyy-MM-dd'T'HH:mm") < now.plus({ hours: 6 })) {
                form.querySelector('input[name="start"]').setCustomValidity('invalid');
            } else {
                form.querySelector('input[name="start"]').setCustomValidity('');
            }
        });
    });
    document.getElementById('deleteSlotForm').addEventListener('submit', function (event) {
        const form = this;
        checkValidity(form, event, () => {
            const id = form.querySelector('div[name="id"]').getAttribute('data-id');
            if (form.querySelector('div[name="claimed"]').getAttribute('data-claimed') == 'true') {
                const start = form.querySelector('input[name="start"]').value;
                const temp = document.createElement('form');
                const data = {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    id: id,
                    start: start
                };
                document.body.appendChild(temp);
                temp.method = 'POST';
                temp.action = '/cancel';
                for (let name in data) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = data[name];
                    temp.appendChild(input);
                }
                temp.submit();
            } else {
                const repeat = form.querySelector('input[name="repeat"]').checked;
                postData('/api/slot/cancel', {
                    id: id,
                    repeat: repeat
                }).then(response => {
                    toggleModal('deleteSlot');
                    toggleAlert(response);
                    calendar.refetchEvents();
                    reportInit();
                });
            }
        });
    });
} else if (accountType == 'student') {
    window.Components = {
        customSelect(options) {
            return {
                init() {
                    this.$refs.listbox.focus()
                    this.optionCount = this.$refs.listbox.children.length
                    this.$watch('selected', value => {
                        if (!this.open) return

                        if (this.selected === null) {
                            this.activeDescendant = ''
                            return
                        }
                        this.activeDescendant = this.$refs.listbox.children[this.selected - 1].id
                    })
                },
                activeDescendant: null,
                optionCount: null,
                open: false,
                selected: null,
                value: 1,
                choose(option) {
                    this.value = option
                    this.open = false
                    subjectId = this.value - 1
                    calendar.refetchEvents()
                },
                onButtonClick() {
                    if (this.open) return
                    this.selected = this.value
                    this.open = true
                    this.$nextTick(() => {
                        this.$refs.listbox.focus()
                        this.$refs.listbox.children[this.selected - 1].scrollIntoView({ block: 'nearest' })
                    })
                },
                onOptionSelect() {
                    if (this.selected !== null) {
                        this.value = this.selected
                    }
                    this.open = false
                    this.$refs.button.focus()
                },
                onEscape() {
                    this.open = false
                    this.$refs.button.focus()
                },
                onArrowUp() {
                    this.selected = this.selected - 1 < 1 ? this.optionCount : this.selected - 1
                    this.$refs.listbox.children[this.selected - 1].scrollIntoView({ block: 'nearest' })
                },
                onArrowDown() {
                    this.selected = this.selected + 1 > this.optionCount ? 1 : this.selected + 1
                    this.$refs.listbox.children[this.selected - 1].scrollIntoView({ block: 'nearest' })
                },
                ...options,
            }
        },
    }
    document.getElementById('claimSlotForm').addEventListener('submit', function (event) {
        const form = this;
        checkValidity(form, event, () => {
            const id = form.querySelector('div[name="id"]').getAttribute('data-id');
            const info = form.querySelector('textarea[name="info"]').value;
            postData('/api/slot/claim', {
                id: id,
                info: info
            }).then(response => {
                toggleModal('claimSlot');
                toggleAlert(response);
                calendar.refetchEvents();
                reportInit();
            });
        });
    });
    document.getElementById('unclaimSlotForm').addEventListener('submit', function (event) {
        const form = this;
        checkValidity(form, event, () => {
            const id = form.querySelector('div[name="id"]').getAttribute('data-id');
            const start = form.querySelector('input[name="start"]').value;
            const temp = document.createElement('form');
            const data = {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                id: id,
                start: start
            };
            document.body.appendChild(temp);
            temp.method = 'POST';
            temp.action = '/cancel';
            for (let name in data) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = data[name];
                temp.appendChild(input);
            }
            temp.submit();
        });
    });
}

function checkView() {
    const currentWidth = window.innerWidth;
    if ((currentWidth <= 768 && prevWidth >= 768) || (currentWidth <= 768 && prevWidth == null)) {
        document.querySelector('.fc-timeGridDay-button').click();
    } else if (currentWidth >= 768 && prevWidth <= 768) {
        document.querySelector('.fc-dayGridMonth-button').click();
    }
    prevWidth = window.innerWidth;
}

function processingStatus(isLoading) {
    const processingStatus = document.getElementById('processingStatus');
    if (isLoading) {
        processingStatus.querySelector('svg[name="done"]').style.display = 'none';
        processingStatus.querySelector('svg[name="load"]').style.display = 'block';
    } else {
        processingStatus.querySelector('svg[name="done"]').style.display = 'block';
        processingStatus.querySelector('svg[name="load"]').style.display = 'none';
    }
}

function toggleModal(id) {
    const modalContainer = document.getElementById('modalContainer');
    if (modalContainer.__x.$data.open == false) {
        modalContainer.classList.remove('hidden');
        modalContainer.__x.$data.open = true;
        modalContainer.__x.$data[id + 'Modal'] = true;
    } else {
        modalContainer.__x.$data.open = false;
        modalContainer.__x.$data[id + 'Modal'] = false;
        setTimeout(() => {
            modalContainer.classList.add('hidden');
            document.getElementById(id + 'Form').reset();
            document.getElementById(id + 'Form').classList.remove('was-validated');
        }, 400);
    }
}

function toggleDisplay(form, hide, show) {
    hide.map(query => form.querySelector(query).parentElement.style.display = 'none');
    show.map(query => {
        if (form.querySelector(query).parentElement.classList.contains('flex')) {
            form.querySelector(query).parentElement.style.display = 'flex';
        } else {
            form.querySelector(query).parentElement.style.display = 'block';
        }
    });
}

function matchValue(form, props, elements) {
    elements.map(element => form.querySelector(element).innerText = props[element.split('"')[1]]);
}