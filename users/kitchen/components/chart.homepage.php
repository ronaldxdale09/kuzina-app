<section class="revenue-section">
    <div class="revenue-top">
        <h4>Total Revenue</h4>
        <div class="revenue-filter">
            <select id="revenue-duration" class="custom-select">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
    </div>
    <div class="revenue-total">
        <h3 id="total-revenue">₱0.00</h3>
    </div>
    <div class="chart-wrap">
        <canvas id="revenueChart"></canvas>
    </div>
</section>

<style>
.revenue-section {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.revenue-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.revenue-top h4 {
    font-size: 18px;
    color: #333;
    margin: 0;
}

.custom-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    background: #fff;
    cursor: pointer;
    outline: none;
}

.custom-select:focus {
    border-color: #502121;
}

.revenue-total {
    margin-bottom: 25px;
}

.revenue-total h3 {
    font-size: 28px;
    color: #502121;
    margin: 0;
    font-weight: bold;
}

.chart-wrap {
    position: relative;
    height: 300px;
    margin-top: 20px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize chart with custom configuration
const revenueChartCtx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(revenueChartCtx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Revenue',
            data: [],
            borderColor: '#502121',
            backgroundColor: 'rgba(80, 33, 33, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointBackgroundColor: '#502121',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                titleFont: {
                    size: 14,
                    weight: 'normal'
                },
                bodyFont: {
                    size: 14
                },
                bodyColor: '#fff',
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return '₱' + context.raw.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 12
                    }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f0f0f0'
                },
                ticks: {
                    font: {
                        size: 12
                    },
                    callback: function(value) {
                        return '₱' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    }
                }
            }
        }
    }
});

// Format currency function
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Event listener for duration change
document.getElementById('revenue-duration').addEventListener('change', function() {
    const duration = this.value;
    fetchRevenueData(duration);
});

// Fetch revenue data function with error handling
async function fetchRevenueData(duration) {
    try {
        const response = await fetch(`fetch/chart.revenue.php?duration=${duration}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error || 'Failed to load revenue data');
        }

        // Update total revenue display
        document.getElementById('total-revenue').textContent = formatCurrency(data.totalRevenue);

        // Prepare chart data
        const labels = data.chartData.map(item => item.period);
        const revenueData = data.chartData.map(item => item.revenue);

        // Update chart
        revenueChart.data.labels = labels;
        revenueChart.data.datasets[0].data = revenueData;
        revenueChart.update();

    } catch (error) {
        console.error('Error fetching revenue data:', error);
        document.getElementById('total-revenue').textContent = 'Error loading data';
    }
}

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    fetchRevenueData('daily');
});
</script>