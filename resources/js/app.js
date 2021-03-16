import 'alpinejs';
require('./bootstrap');

document.getElementById('year').innerText = (new Date()).getFullYear();
if (reportEnabled) {
    reportInit();
    document.getElementById('reportType').addEventListener('change', function () { toggleForm(this) });
    document.getElementById('reportBugForm').addEventListener('submit', function (event) {
        const form = this;
        checkValidity(form, event, () => {
            const message = form.querySelector('textarea[name="message"]').value;
            postData('/api/report', {
                type: 1,
                message: message
            }).then(() => {
                document.getElementById('reportContainer').__x.$data.open = false;
                form.reset();
                form.classList.remove('was-validated');
                reportInit();
            });
        });
    });
    document.getElementById('reportPersonForm').addEventListener('submit', function (event) {
        const form = this;
        checkValidity(form, event, () => {
            const slotId = form.querySelector('select[name="start"]').value;
            console.log(slotId);
            postData('/api/report', {
                type: 2,
                slot_id: slotId
            }).then(response => {
                document.getElementById('reportContainer').__x.$data.open = false;
                toggleAlert(response);
                form.reset();
                form.classList.remove('was-validated');
                reportInit();
            });
        });
    });
}