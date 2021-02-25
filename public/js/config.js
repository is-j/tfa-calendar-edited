const DateTime = luxon.DateTime;
const now = DateTime.local();
async function postData(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        cache: 'no-cache',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    });
    return response.json();
}
function checkValidity(element, event, success, validity) {
    event.preventDefault();
    if (validity instanceof Function) {
        (validity)();
    }
    if (!element.checkValidity()) {
        event.stopPropagation();
        element.classList.add('was-validated');
    } else {
        (success)();
        element.classList.remove('was-validated');
    }
}
function reportInit() {
    fetch('/api/report', { method: 'GET' }).then(response => response.json()).then(data => {
        if (data.success) {
            document.getElementById('reportType').removeAttribute('disabled');
            document.getElementById('reportPersonForm').querySelector('select[name="start"]').innerHTML = '';
            for (let slot of data.slots) {
                document.getElementById('reportPersonForm').querySelector('select[name="start"]').insertAdjacentHTML('afterbegin', `<option value="${slot.id}">${DateTime.fromSQL(slot.start, { zone: 'UTC' }).toLocal().toFormat('ff')}</option>`);
            }
        } else {
            document.getElementById('reportType').value = '1';
            toggleForm(document.getElementById('reportType'));
            document.getElementById('reportType').setAttribute('disabled', true);
        }
    });
}
function toggleForm(select) {
    if (select.value == 1) {
        document.getElementById('reportBugForm').style.display = 'block';
        document.getElementById('reportPersonForm').style.display = 'none';
        document.getElementById('reportContainer').__x.$data.reportForm = 'reportBugForm';
    } else if (select.value == 2) {
        document.getElementById('reportBugForm').style.display = 'none';
        document.getElementById('reportPersonForm').style.display = 'block';
        document.getElementById('reportContainer').__x.$data.reportForm = 'reportPersonForm';
    }
}
function toggleAlert(response) {
    if (response.error) {
        document.getElementById('alertText').innerText = response.message;
        document.getElementById('alertContainer').__x.$data.open = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}