import 'tabulator-tables/dist/css/tabulator.min.css';
import { TabulatorFull as Tabulator } from 'tabulator-tables';

const container = document.getElementById('activity-schedule-enrolled-table');
if (container && !container.dataset.tabulatorInit) {
  container.dataset.tabulatorInit = '1';
  const endpoint = container.dataset.endpoint;
  let table; // forward ref
  const dynamicHeight = (count) => {
    const perRow = 38; // aproximado (depende de fuente/padding)
    const header = 44;
    const minH = 120;
    const maxH = 420; // límite para no crecer infinito
    const calc = count === 0 ? minH : header + (count * perRow) + 8;
    const finalH = Math.min(maxH, Math.max(minH, calc));
    table.setHeight(finalH + 'px');
  };

  table = new Tabulator(container, {
    ajaxURL: endpoint,
    layout: 'fitColumns',
    height: '120px', // se ajustará tras primera carga
    placeholder: 'Sin usuarios inscritos',
    responsiveLayout: 'collapse',
    columnDefaults: { hozAlign: 'center', headerHozAlign: 'center', vertAlign: 'middle' },
    ajaxResponse: (_u,_p,r)=> {
      const rows = r.data ?? [];
      queueMicrotask(()=> dynamicHeight(rows.length));
      return rows;
    },
    columns: [
      { title: 'ID', field: 'id', width: 70, sorter: 'number' },
      { title: 'Nombre', field: 'name', responsive: 0 },
      { title: 'Email', field: 'email', responsive: 1 },
      { title: 'Asistió', field: 'attended', width: 90, hozAlign: 'center', formatter: (cell)=> {
          const v = cell.getValue();
          return `<input type='checkbox' ${v ? 'checked' : ''} class='attend-toggle' />`;
        }, cellClick: (e, cell) => {
          const current = !!cell.getValue();
          cell.setValue(!current, true);
        }
      },
    ],
    initialSort: [{ column: 'name', dir: 'asc' }],
  });

  // Simple auto-refresh every 60s in case of new enrollments
  if (window.__enrolledUsersInterval) clearInterval(window.__enrolledUsersInterval);
  window.__enrolledUsersInterval = setInterval(()=> table.replaceData(), 60_000);

  // Attendance save button
  const saveBtn = document.getElementById('save-attendance');
  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      saveBtn.disabled = true;
      const endpointUpdate = saveBtn.dataset.endpoint;
      // Collect current data
      const records = table.getData().map(r => ({ id: r.id, attended: !!r.attended }));
      try {
        const resp = await fetch(endpointUpdate, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({ records })
        });
        if (!resp.ok) throw new Error('Error guardando');
        // Refresh after save
        table.replaceData();
        saveBtn.textContent = 'Guardado';
        setTimeout(()=> saveBtn.textContent = 'Guardar asistencia', 2000);
      } catch (e) {
        console.error(e);
        saveBtn.textContent = 'Error';
        setTimeout(()=> saveBtn.textContent = 'Guardar asistencia', 2500);
      } finally {
        saveBtn.disabled = false;
      }
    });
  }
}
