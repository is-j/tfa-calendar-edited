document.getElementById('cancelSlotForm').addEventListener('submit', function (event) {
    const form = this;
    checkValidity(form, event, () => {
        const id = form.querySelector('div[name="id"]').getAttribute('data-id');
        const reason = form.querySelector('textarea[name="reason"]').value;
        postData('/api/slot/cancel', {
            id: id,
            reason: reason
        }).then(response => {
            if (response.error) {
                const temp = document.createElement('form');
                const data = {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    alert: response.message
                };
                document.body.appendChild(temp);
                temp.method = 'POST';
                temp.action = '/dashboard';
                for (let name in data) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = data[name];
                    temp.appendChild(input);
                }
                temp.submit();
            } else {
                location.href = '/dashboard';
            }
        });
    });
});