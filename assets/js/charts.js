/**
 * assets/js/charts.js
 * Fetches chart data from /api/get_chart_data.php and renders three Chart.js charts.
 * Requires Chart.js to be loaded before this script.
 */

const CHART_COLORS = {
    blue:   '#4F6EF7',
    green:  '#22C55E',
    orange: '#F59E0B',
    red:    '#EF4444',
    purple: '#A855F7',
    teal:   '#14B8A6',
    pink:   '#EC4899',
};

const PIE_PALETTE = [
    CHART_COLORS.blue,
    CHART_COLORS.green,
    CHART_COLORS.orange,
    CHART_COLORS.purple,
    CHART_COLORS.teal,
    CHART_COLORS.pink,
];

Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
Chart.defaults.color       = '#94A3B8';

async function initCharts() {
    let data;
    try {
        const res = await fetch('/api/get_chart_data.php');
        data = await res.json();
        if (!data.success) return;
    } catch (e) {
        console.error('Failed to load chart data', e);
        return;
    }

    /* ── 1. Rating Bar Chart ──────────────────────────────────────── */
    const barCtx = document.getElementById('ratingBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: data.ratings.labels.map(l => `${l} ⭐`),
                datasets: [{
                    label: 'Number of Feedbacks',
                    data:  data.ratings.data,
                    backgroundColor: [
                        '#EF4444', '#F59E0B', '#FACC15', '#22C55E', '#4F6EF7',
                    ],
                    borderRadius:     8,
                    borderSkipped:    false,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} feedback(s)`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(255,255,255,0.05)' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }

    /* ── 2. Category Pie Chart ────────────────────────────────────── */
    const pieCtx = document.getElementById('categoryPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels:   data.categories.labels,
                datasets: [{
                    data:            data.categories.data,
                    backgroundColor: PIE_PALETTE,
                    borderWidth:     2,
                    borderColor:     '#1E293B',
                    hoverOffset:     6,
                }],
            },
            options: {
                responsive: true,
                cutout:     '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels:   { padding: 16, boxWidth: 14 },
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} (${
                                Math.round(ctx.parsed / ctx.dataset.data.reduce((a, b) => a + b, 0) * 100)
                            }%)`,
                        },
                    },
                },
            },
        });
    }

    /* ── 3. Monthly Line Chart ────────────────────────────────────── */
    const lineCtx = document.getElementById('monthlyLineChart');
    if (lineCtx) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels:   data.monthly.labels,
                datasets: [{
                    label:           'Feedbacks Submitted',
                    data:             data.monthly.data,
                    borderColor:      CHART_COLORS.blue,
                    backgroundColor:  'rgba(79,110,247,0.12)',
                    borderWidth:      2.5,
                    pointRadius:      4,
                    pointHoverRadius: 6,
                    tension:          0.4,
                    fill:             true,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} feedback(s)`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks:       { precision: 0 },
                        grid:        { color: 'rgba(255,255,255,0.05)' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }

    /* ── Recent feedback table on admin dashboard ─────────────────── */
    const recentContainer = document.getElementById('recentFeedbackTable');
    if (recentContainer) {
        try {
            const fbRes  = await fetch('/api/get_feedbacks.php');
            const fbData = await fbRes.json();

            if (!fbData.success || !fbData.feedbacks.length) {
                recentContainer.innerHTML =
                    '<p class="table-empty"><i class="fa-solid fa-inbox"></i> No feedbacks yet.</p>';
                return;
            }

            const recent = fbData.feedbacks.slice(0, 5);
            let html = `<table class="data-table">
                <thead><tr>
                    <th>Employee</th><th>Category</th><th>Rating</th><th>Message</th><th>Date</th>
                </tr></thead><tbody>`;
            recent.forEach(f => {
                const cat = f.category.replace(/_/g, ' ')
                              .replace(/\b\w/g, c => c.toUpperCase());
                html += `<tr>
                    <td>${f.anonymous ? '<em class="muted">Anonymous</em>' : escHtml(f.employee_name || '')}</td>
                    <td><span class="badge badge-category">${cat}</span></td>
                    <td>${'⭐'.repeat(f.rating)}</td>
                    <td class="message-cell">${escHtml(f.message)}</td>
                    <td class="nowrap">${escHtml(f.created_at)}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            recentContainer.innerHTML = html;
        } catch (e) {
            recentContainer.innerHTML =
                '<p class="table-error"><i class="fa-solid fa-triangle-exclamation"></i> Failed to load recent feedbacks.</p>';
        }
    }
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

initCharts();
