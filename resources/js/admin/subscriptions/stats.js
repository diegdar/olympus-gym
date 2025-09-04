import 'tabulator-tables/dist/css/tabulator.min.css';
import { TabulatorFull as Tabulator } from 'tabulator-tables';

// Utilidades ---------------------------------------------------------------
const qs = (id) => document.getElementById(id);
const PERCENTAGES_ID = 'subscription-percentages-table';
const MONTHLY_NET_ID = 'subscription-monthly-net-table';
const YEAR_SELECT_ID = 'subscription-year-select';

function percentageBarFormatter(cell) {
    const value = cell.getValue() ?? 0;
    const safe = Math.max(0, Math.min(100, parseFloat(value)));
    const display = Number.isInteger(safe) ? safe : safe.toFixed(2);
    return `<div class="w-full flex items-center gap-2">
        <div class=\"flex-1 bg-zinc-200 dark:bg-zinc-700 rounded h-3 overflow-hidden\">
            <div class=\"h-full bg-emerald-500 transition-all duration-500\" style=\"width:${safe}%;\"></div>
        </div>
        <span class=\"text-xs font-medium tabular-nums\">${display}%</span>
    </div>`;
}

function buildPercentagesTable(el) {
    if (el.dataset.tabulatorInit) return;
    el.dataset.tabulatorInit = '1';

    const table = new Tabulator(el, {
        ajaxURL: el.dataset.endpoint,
        ajaxConfig: 'GET',
        layout: 'fitColumns',
        responsiveLayout: 'collapse',
        placeholder: 'Sin datos',
        height: '120px',
        columnDefaults: { hozAlign: 'center', headerHozAlign: 'center', vertAlign: 'middle' },
        columns: [
            { title: 'Cuota', field: 'fee_translated', responsive: 0 },
            { title: 'Usuarios', field: 'users', responsive: 1 },
            { title: '%', field: 'percentage', sorter: 'number', widthGrow: 2, formatter: percentageBarFormatter, responsive: 2 },
        ],
        ajaxResponse: (_url, _params, resp) => {
            const totalInfo = qs('subscription-percentages-total');
            if (totalInfo) totalInfo.textContent = `Total usuarios activos: ${resp.total_active_users}`;
            return resp.data;
        },
        initialSort: [{ column: 'percentage', dir: 'desc' }],
    });

    if (window.__subscriptionPercentagesInterval) clearInterval(window.__subscriptionPercentagesInterval);
    window.__subscriptionPercentagesInterval = setInterval(() => table.replaceData(), 60_000);
}

function buildMonthlyNetTable(selectEl, container) {
    if (container.dataset.tabulatorInit) return;
    container.dataset.tabulatorInit = '1';
    const endpoint = selectEl.dataset.endpoint;

    const netTable = new Tabulator(container, {
        ajaxURL: endpoint,
        ajaxParams: { year: selectEl.value },
        layout: 'fitColumns',
        responsiveLayout: 'collapse',
        placeholder: 'Sin datos',
        height: '380px',
        columnDefaults: { hozAlign: 'center', headerHozAlign: 'center' },
        ajaxResponse: (_u,_p,r)=> r.data ?? [],
        columns: [
            { title: 'Mes', field: 'month_name', responsive: 0, sorter: (_a,_b,rowA,rowB)=> parseInt(rowA.getData().month,10) - parseInt(rowB.getData().month,10) },
            { title: 'Altas', field: 'signups', sorter: 'number', responsive: 1 },
            { title: 'Bajas', field: 'cancellations', sorter: 'number', responsive: 2 },
            { title: 'Neto', field: 'net', sorter: 'number', responsive: 3, formatter: (cell)=> {
                const v = cell.getValue();
                const color = v > 0 ? 'text-emerald-600' : (v < 0 ? 'text-red-600' : 'text-zinc-600');
                return `<span class="font-semibold ${color}">${v}</span>`;
            } },
        ],
    });

    const exportJson = qs('export-monthly-json');
    const exportExcel = qs('export-monthly-excel');
    const updateLinks = () => {
        const y = selectEl.value;
        if (exportJson) exportJson.href = `${endpoint}/export/json?year=${y}`;
        if (exportExcel) exportExcel.href = `${endpoint}/export/excel?year=${y}`;
    };
    updateLinks();

    selectEl.addEventListener('change', () => {
        netTable.setData(endpoint, { year: selectEl.value });
        updateLinks();
    });
}

function init() {
    const percentages = qs(PERCENTAGES_ID);
    if (!percentages) return; // página no presente
    buildPercentagesTable(percentages);
    const yearSelect = qs(YEAR_SELECT_ID);
    const monthlyNet = qs(MONTHLY_NET_ID);
    if (yearSelect && monthlyNet) buildMonthlyNetTable(yearSelect, monthlyNet);
}

['DOMContentLoaded','livewire:navigated','flux:navigate'].forEach(evt => window.addEventListener(evt, init));

// Fallback (solo si eventos SPA no disparan):
if (!window.__subscriptionStatsObserver) {
    window.__subscriptionStatsObserver = new MutationObserver(() => {
        const el = qs(PERCENTAGES_ID);
        if (el && !el.dataset.tabulatorInit) init();
    });
    window.__subscriptionStatsObserver.observe(document.body, { childList: true, subtree: true });
}
