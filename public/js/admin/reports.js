reportGet();
document.getElementById('tableBody').addEventListener('click', (event) => {
    if (event.target.getAttribute('data-action') != null) {
        postData(`/api/report/${event.target.getAttribute('data-action')}`, {
            slot_id: event.target.getAttribute('data-slot')
        }).then(() => {
            reportGet();
        });
    }
});

function reportGet() {
    processingStatus(true);
    fetch('/api/admin/reports', { method: 'GET' }).then(response => response.json()).then(data => {
        let temp = '';
        for (report of data) {
            if (report.confirmed === null) {
                temp += `<tr> <td class="px-6 py-4 whitespace-nowrap"> <div class="ml-4"> <div class="text-sm font-medium text-gray-900"> ${report.reporter_info} </div> <div class="text-sm text-gray-500">${report.reporter_email} </div> </div> </td> <td class="px-5 py-4 whitespace-nowrap"> <div class="text-sm text-gray-900">${report.reported_info}</div> <div class="text-sm text-gray-500">${report.reported_email}</div> </td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> ${DateTime.fromSQL(report.slot_start, { zone: 'UTC' }).toLocal().toFormat('ff')} </td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> ${DateTime.fromSQL(report.created_at, { zone: 'UTC' }).toLocal().toFormat('ff')} </td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> <button type="button" class="btn-positive mr-3 inline-block" data-action="confirm" data-slot="${report.slot_id}"><svg class="inline-block align-middle h-7 w-7 pointer-events-none cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /> </svg></button> <button type="button" class="btn-negative inline-block" data-action="deny" data-slot="${report.slot_id}"><svg class="inline-block align-middle h-7 w-7 pointer-events-none cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /> </svg></button> </td> </tr>`;
            } else {
                temp += `<tr> <td class="px-6 py-4 whitespace-nowrap"> <div class="ml-4"> <div class="text-sm font-medium text-gray-900"> ${report.reporter_info} </div> <div class="text-sm text-gray-500">${report.reporter_email} </div> </div> </td> <td class="px-5 py-4 whitespace-nowrap"> <div class="text-sm text-gray-900">${report.reported_info}</div> <div class="text-sm text-gray-500">${report.reported_email}</div> </td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> ${DateTime.fromSQL(report.slot_start, { zone: 'UTC' }).toLocal().toFormat('ff')} </td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> ${DateTime.fromSQL(report.created_at, { zone: 'UTC' }).toLocal().toFormat('ff')} </td> <td class="px-6 py-4 whitespace-nowrap"> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${(report.confirmed) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}"> ${(report.confirmed) ? 'Confirmed' : 'Denied'} </span> </td> </tr>`;
            }
        }
        document.getElementById('tableBody').innerHTML = temp;
        processingStatus(false);
    });
}