if (accountType == 'tutor') {
    const minusHTML = '<svg class="h-6 w-6 text-red-600 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /> </svg>';
    const plusHTML = '<svg class="h-6 w-6 text-green-600 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /> </svg>';
    fetch('/api/subject/get', { method: 'GET' }).then(response => response.json()).then(data => {
        for (subject of data[0]) {
            document.getElementById('mainContent').insertAdjacentHTML('afterbegin', `<li class="list-group-item select-none bg-white">${subject.name}<span class="float-right cursor-pointer" data-subject="${subject.id}">${minusHTML}</span></li>`, document.getElementById('subjectError'));
        }
        for (subject of data[1]) {
            document.getElementById('searchContent').insertAdjacentHTML('afterbegin', `<li class="list-group-item select-none bg-white">${subject.name}<span class="float-right cursor-pointer" data-subject="${subject.id}">${plusHTML}</span></li>`);
        }
    });
    document.getElementById('searchContent').addEventListener('click', (event) => {
        if (event.target.getAttribute('data-subject') != null) {
            const subjectId = event.target.getAttribute('data-subject');
            postData('/api/subject/plus', {
                subject_id: subjectId
            }).then(() => {
                event.target.innerHTML = minusHTML;
                document.getElementById('mainContent').insertAdjacentElement('afterbegin', document.getElementById('searchContent').removeChild(event.target.parentElement));
            });
        }
    });
    document.getElementById('mainContent').addEventListener('click', (event) => {
        if (event.target.getAttribute('data-subject') != null) {
            const subjectId = event.target.getAttribute('data-subject');
            postData('/api/subject/minus', {
                subject_id: subjectId
            }).then((response) => {
                if (response.success) {
                    event.target.innerHTML = plusHTML;
                    document.getElementById('searchContent').insertAdjacentElement('afterbegin', document.getElementById('mainContent').removeChild(event.target.parentElement));
                } else {
                    const subjectError = document.getElementById('subjectError');
                    subjectError.classList.remove('hidden', 'opacity-0');
                    setTimeout(() => {
                        subjectError.classList.add('opacity-0');
                        setTimeout(() => subjectError.classList.add('hidden'), 400);
                    }, 1250);
                }
            });
        }
    });
    document.getElementById('searchInput').addEventListener('keyup', () => {
        const value = document.getElementById('searchInput').value.toLowerCase();
        Array.prototype.filter.call(document.getElementById('searchContent').querySelectorAll('li'), (element) => {
            if (element.innerText.toLowerCase().indexOf(value) > -1) {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        });
    });
    document.getElementById('informationForm').addEventListener('submit', function (event) {
        checkValidity(this, event, () => {
            const meetingLink = this.querySelector('input[name="meeting_link"]').value;
            const bio = this.querySelector('textarea[name="bio"]').value;
            this.querySelector('fieldset').setAttribute('disabled', true);
            this.querySelector('button').setAttribute('disabled', true);
            this.querySelector('button').innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"> <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle> <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path> </svg> Updating...';
            postData('/api/information/update', {
                meeting_link: meetingLink,
                bio: bio
            }).then(() => setTimeout(() => {
                this.querySelector('fieldset').removeAttribute('disabled');
                this.querySelector('button').removeAttribute('disabled')
                this.querySelector('button').innerHTML = 'Update information';
            }, 1000));
        });
    });
}