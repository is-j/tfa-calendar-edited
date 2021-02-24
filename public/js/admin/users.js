processingStatus(true);
fetch('/api/admin/users', { method: 'GET' }).then(response => response.json()).then(data => {
    let temp = '';
    for (user of data) {
        temp += `<tr> <td class="px-6 py-4 whitespace-nowrap"> <div class="ml-4"> <div class="text-sm font-medium text-gray-900"> ${user.name} </div> <div class="text-sm text-gray-500">${user.email} </div> </div> </td> <td class="px-6 py-4 whitespace-nowrap"> <div class="text-sm text-gray-900">${(user.probation !== null) ? `Been suspended ${user.probation} ${(user.probation == 1) ? 'time' : 'times'}` : 'Never been suspended'}</div > <div class="text-sm text-gray-500">${user.strikes} strikes</div> </td > <td class="px-6 py-4 whitespace-nowrap"> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${(user.status == 'Active') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}"> ${user.status} </span> </td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"> ${user.role} </td> </tr>`;
    }
    document.getElementById('tableBody').insertAdjacentHTML('afterbegin', temp);
    processingStatus(false);
});