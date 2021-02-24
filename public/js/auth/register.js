const offset = DateTime.local().toFormat('ZZZ');
const cleanOffset = parseInt(offset.slice(0, 3)) + parseInt(offset.slice(3, 5)) / 60;
document.getElementById('offset').value = cleanOffset;