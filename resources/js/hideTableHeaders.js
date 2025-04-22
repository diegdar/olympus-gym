document.addEventListener('DOMContentLoaded', () => {
    const headers = Array.from(document.querySelectorAll('table thead th')).map(th => th.textContent.trim());
    document.querySelectorAll('table tbody tr').forEach(tr => {
        Array.from(tr.querySelectorAll('td')).forEach((td, index) => {
            td.setAttribute('data-label', headers[index] + ':');
        });
    });
});
