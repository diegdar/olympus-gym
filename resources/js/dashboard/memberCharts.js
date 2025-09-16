// Lazy-load Chart.js and plugins only when needed to improve LCP
let chartLibPromise = null;
function loadChartLibs() {
	if (!chartLibPromise) {
		chartLibPromise = Promise.all([
			import('chart.js/auto'),
			import('chartjs-plugin-datalabels')
		]).then(([chartMod, datalabelsMod]) => {
			const Chart = chartMod.default;
			const ChartDataLabels = datalabelsMod.default;
			Chart.register(ChartDataLabels);
			return { Chart };
		});
	}
	return chartLibPromise;
}

function fetchJSON(endpoint) {
	return fetch(endpoint, { headers: { 'Accept': 'application/json' } })
		.then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); });
}

function weeklyAttendanceChart() {
	const el = document.getElementById('member-weekly-attendance-chart');
	if (!el || el.dataset.inited || el.dataset.initializing) return;
	el.dataset.initializing = '1';
	Promise.all([
		loadChartLibs(),
		fetchJSON(el.dataset.endpoint)
	]).then(([{ Chart }, data]) => {
		new Chart(el, {
			type: 'line',
			data: {
				labels: data.labels,
				datasets: [{
					label: 'Asistencias',
					data: data.values,
					borderColor: '#6366f1',
					backgroundColor: 'rgba(99,102,241,.15)',
					tension: .35,
					fill: true,
					pointRadius: 3
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
				plugins: { datalabels: { display: false } }
			}
		});
		el.dataset.inited = '1';
	}).catch((err) => {
		console.error(err);
		delete el.dataset.initializing;
	});
}

function activityDistributionChart() {
	const el = document.getElementById('member-activity-distribution-chart');
	if (!el || el.dataset.inited || el.dataset.initializing) return;
	el.dataset.initializing = '1';
	Promise.all([
		loadChartLibs(),
		fetchJSON(el.dataset.endpoint)
	]).then(([{ Chart }, data]) => {
		const total = (data.values || []).reduce((a, b) => a + (Number(b) || 0), 0) || 0;
		const baseColors = ['#10b981','#6366f1','#f59e0b','#ef4444','#8b5cf6','#0ea5e9'];
		const colors = (data.labels || []).map((_, i) => baseColors[i % baseColors.length]);
		new Chart(el, {
			type: 'doughnut',
			data: {
				labels: data.labels,
				datasets: [{
					data: data.values,
					backgroundColor: colors
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					datalabels: {
						color: '#fff',
						font: { weight: 'bold', size: 20 },
						formatter: (value) => {
							if (!total) return null;
							const pct = (Number(value) || 0) / total * 100;
							if (pct < 4) return null; // hide very small slices
							return pct.toFixed(0) + '%';
						},
						anchor: 'center',
						align: 'center',
						clamp: true
					}
				},
				cutout: '60%'
			}
		});

		// Render custom HTML legend: one item per line with spacing between color and label
		const legendContainer = document.getElementById('member-activity-distribution-legend');
		if (legendContainer) {
			const labels = data.labels || [];
			legendContainer.innerHTML = labels.map((label, i) => `
				<div class="flex items-center gap-2">
					<span class="inline-block w-3 h-3 rounded-full" style="background-color: ${colors[i]};"></span>
					<span>${label}</span>
				</div>
			`).join('');
		}
		el.dataset.inited = '1';
	}).catch((err) => {
		console.error(err);
		delete el.dataset.initializing;
	});
}

function observeAndInit() {
	const weeklyEl = document.getElementById('member-weekly-attendance-chart');
	const doughnutEl = document.getElementById('member-activity-distribution-chart');

	// If IntersectionObserver is available, defer rendering until visible
	if ('IntersectionObserver' in window) {
		const io = new IntersectionObserver((entries, obs) => {
			for (const entry of entries) {
				if (!entry.isIntersecting) continue;
				const el = entry.target;
				if (el.id === 'member-weekly-attendance-chart') weeklyAttendanceChart();
				if (el.id === 'member-activity-distribution-chart') activityDistributionChart();
				obs.unobserve(el);
			}
		}, { threshold: 0.15 });

		if (weeklyEl && !weeklyEl.dataset.inited) io.observe(weeklyEl);
		if (doughnutEl && !doughnutEl.dataset.inited) io.observe(doughnutEl);
	} else {
		// Fallback: initialize immediately
		weeklyAttendanceChart();
		activityDistributionChart();
	}
}

function init() {
	observeAndInit();
}

// Run once immediately if DOM is already parsed
if (document.readyState === 'complete' || document.readyState === 'interactive') {
	queueMicrotask(init);
}

// Also wire up to common navigation/ready events
['DOMContentLoaded','livewire:navigated','flux:navigate','pageshow'].forEach(e => window.addEventListener(e, init));

// Fallback: slight delay after load to catch late DOM swaps
setTimeout(init, 0);

