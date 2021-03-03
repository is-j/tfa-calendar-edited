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
            temp += `<tr> <td class="px-6 py-4 whitespace-nowrap"> <div class="ml-4 text-sm font-medium text-gray-900"> ${subject.id} </div> </td> <td class="px-6 py-4 text-sm text-gray-500"> ${subject.name} </td> </tr> </tr> </tr>`;
        }
        document.getElementById('tableBody').innerHTML = temp;
        processingStatus(false);
    });
}