document.getElementById('searchInput').addEventListener('keyup', () => {
    const value = document.getElementById('searchInput').value.toLowerCase();
    Array.prototype.filter.call(document.getElementById('tableBody').querySelectorAll('tr'), (element) => {
        if (element.innerText.toLowerCase().indexOf(value) > -1) {
            element.style.display = 'table-row';
        } else {
            element.style.display = 'none';
        }
    });
});
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