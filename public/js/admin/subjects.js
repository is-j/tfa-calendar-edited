subjectGet();
document.getElementById('createSubject').addEventListener('click', function (event) {
    if (this.querySelector('input').value != '') {
        postData('/api/subject/create', {
            subject_name: this.querySelector('input').value
        }).then(response => {
            if (response.success) {
                subjectGet();
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
function subjectGet() {
    processingStatus(true);
    fetch('/api/admin/subjects', { method: 'GET' }).then(response => response.json()).then(data => {
        let temp = '';
        for (subject of data) {
            temp += `<tr> <td class="px-6 py-4 whitespace-nowrap"> <div class="ml-4 text-sm font-medium text-gray-900"> ${subject.id} </div> </td> <td class="px-6 py-4 text-sm text-gray-500"> ${subject.name} </td> <td class="px-6 py-4 text-sm text-gray-500 float-right flex"> <span class="cursor-pointer" data-action="edit"><svg class="h-7 w-7 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /> </svg></span> <span class="cursor-pointer ml-3" data-action="delete"><svg class="h-7 w-7 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /> </svg></span> </td> </tr> </tr> </tr>`;
        }
        document.getElementById('tableBody').innerHTML = temp;
        processingStatus(false);
    });
}
document.getElementById('tableBody').addEventListener('click', (event) => {
    if (event.target.getAttribute('data-action') == 'edit') {
        
    } else if (event.target.getAttribute('data-action') == 'delete') {

    }
});