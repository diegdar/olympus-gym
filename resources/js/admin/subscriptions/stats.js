import 'tabulator-tables/dist/css/tabulator.min.css';
import { TabulatorFull as Tabulator } from 'tabulator-tables';

// Idempotent init so it works with SPA-style (wire:navigate / flux) navigation.
function initSubscriptionStats() {
    const el = document.getElementById('subscription-percentages-table');
    if (!el || el.dataset.tabulatorInit) return; // already initialized or not present
    el.dataset.tabulatorInit = '1';

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

    const percentagesTable = new Tabulator(el, {
        ajaxURL: endpoint,
        ajaxConfig: 'GET',
        layout: 'fitColumns',
        responsiveLayout: 'collapse',
        placeholder: 'Sin datos',
        height: '120px',
        columnDefaults: {
            hozAlign: 'center',
            headerHozAlign: 'center',
            vertAlign: 'middle',
        },
        columns: [
            { title: 'Cuota', field: 'fee_translated', responsive: 0 },
            { title: 'Usuarios', field: 'users', responsive: 1 },
            { title: '%', field: 'percentage', sorter: 'number', widthGrow: 2, formatter: percentageBarFormatter, responsive: 2 },
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

    // Avoid stacking multiple intervals across navigations
    if (window.__subscriptionPercentagesInterval) {
        clearInterval(window.__subscriptionPercentagesInterval);
    }
    window.__subscriptionPercentagesInterval = setInterval(() => percentagesTable.replaceData(), 60_000);

    // Monthly net stats
    const yearSelect = document.getElementById('subscription-year-select');
    const netContainer = document.getElementById('subscription-monthly-net-table');
    if (yearSelect && netContainer && !netContainer.dataset.tabulatorInit) {
        netContainer.dataset.tabulatorInit = '1';
        const netEndpoint = yearSelect.dataset.endpoint;
        const netTable = new Tabulator(netContainer, {
            ajaxURL: netEndpoint,
            ajaxParams: { year: yearSelect.value },
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'Sin datos',
            height: '360px',
            columnDefaults: { hozAlign: 'center', headerHozAlign: 'center' },
            ajaxResponse: function(_url, _params, response){
                return response.data ?? [];
            },
            columns: [
                { title: 'Mes', field: 'month_name', responsive: 0, sorter: (a,b,rowA,rowB)=> {
                    const ma = parseInt(rowA.getData().month,10);
                    const mb = parseInt(rowB.getData().month,10);
                    return ma - mb;
                } },
                { title: 'Altas', field: 'signups', sorter: 'number', responsive: 1 },
                { title: 'Bajas', field: 'cancellations', sorter: 'number', responsive: 2 },
                { title: 'Neto', field: 'net', sorter: 'number', responsive: 3, formatter: (cell)=> {
                    const v = cell.getValue();
                    const color = v > 0 ? 'text-emerald-600' : (v < 0 ? 'text-red-600' : 'text-zinc-600');
                    return `<span class="font-semibold ${color}">${v}</span>`;
                } },
            ],
        });

        yearSelect.addEventListener('change', () => {
            netTable.setData(netEndpoint, { year: yearSelect.value });
            updateExportLinks();
        });

        const exportJson = document.getElementById('export-monthly-json');
        const exportExcel = document.getElementById('export-monthly-excel');
        function updateExportLinks(){
            if (exportJson) {
                exportJson.href = `${netEndpoint}/export/json?year=${yearSelect.value}`;
            }
            if (exportExcel) {
                exportExcel.href = `${netEndpoint}/export/excel?year=${yearSelect.value}`;
            }
        }
        updateExportLinks();
    }
}

// Standard first load
document.addEventListener('DOMContentLoaded', initSubscriptionStats);
// Livewire navigate (wire:navigate) event (common name)
window.addEventListener('livewire:navigated', initSubscriptionStats);
// Flux / custom navigation (if provided by your UI kit)
window.addEventListener('flux:navigate', initSubscriptionStats);

// Fallback: observe DOM mutations (disconnect after successful init)
if (!window.__subscriptionStatsObserver) {
    window.__subscriptionStatsObserver = new MutationObserver(() => {
        if (document.getElementById('subscription-percentages-table') && !document.getElementById('subscription-percentages-table').dataset.tabulatorInit) {
            initSubscriptionStats();
        }
    });
    window.__subscriptionStatsObserver.observe(document.body, { childList: true, subtree: true });
}
