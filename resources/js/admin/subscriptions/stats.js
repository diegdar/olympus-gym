import 'tabulator-tables/dist/css/tabulator.min.css';
import { TabulatorFull as Tabulator } from 'tabulator-tables';

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('subscription-percentages-table');
    if (!el) return;

    const endpoint = el.dataset.endpoint;

    // format percentage as a bar with percentage text
    const percentageBarFormatter = (cell) => {
        const value = cell.getValue() ?? 0;
        const safe = Math.max(0, Math.min(100, parseFloat(value)));
        const display = Number.isInteger(safe) ? safe : safe.toFixed(2);
        return `
            <div class="w-full flex items-center gap-2">
                <div class="flex-1 bg-zinc-200 dark:bg-zinc-700 rounded h-3 overflow-hidden">
                    <div class="h-full bg-emerald-500 transition-all duration-500" style="width:${safe}%;"></div>
                </div>
                <span class="text-xs font-medium tabular-nums">${display}%</span>
            </div>
        `;
    };

    const table = new Tabulator(el, {
        ajaxURL: endpoint,
        ajaxConfig: 'GET',
        layout: 'fitColumns',
        placeholder: 'Sin datos',
        height: '120px',
        columnDefaults: {
            hozAlign: 'center',
            headerHozAlign: 'center',
            vertAlign: 'middle',
        },
        columns: [
            { title: 'Cuota', field: 'fee_translated' },
            { title: 'Usuarios', field: 'users' },
            { title: '%', field: 'percentage', sorter: 'number', widthGrow: 2, formatter: percentageBarFormatter },
        ],
        ajaxResponse: function (_url, _params, response) {
            const totalInfo = document.getElementById('subscription-percentages-total');
            if (totalInfo) {
                totalInfo.textContent = `Total usuarios activos: ${response.total_active_users}`;
            }
            return response.data;
        },
        initialSort: [
            { column: 'percentage', dir: 'desc' }
        ],
    });

    // refresh every 5 minutes
    setInterval(() => table.replaceData(), 60_000);
});
