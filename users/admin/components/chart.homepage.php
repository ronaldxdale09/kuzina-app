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
        <h3 id="total-revenue">Php 0.00</h3>
        <!-- <a href="#" class="details-link">See Details</a> -->
    </div>
    <!-- Chart Placeholder -->
    <div class="chart-wrap">
        <canvas id="revenueChart"></canvas>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueChartCtx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(revenueChartCtx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Revenue',
            data: [],
            borderColor: '#FF6B6B',
            fill: false
        }]
    },
    options: {
        scales: {
            x: {
                display: true
            },
            y: {
                display: true,
                beginAtZero: true
            }
        }
    }
});

document.getElementById('revenue-duration').addEventListener('change', function() {
    const duration = this.value;
    fetchRevenueData(duration);
});

function fetchRevenueData(duration) {
    fetch(`fetch/get_revenue.php?duration=${duration}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-revenue').textContent = `Php ${data.totalRevenue.toFixed(2)}`;

            const labels = data.chartData.map(item => item.period);
            const revenueData = data.chartData.map(item => item.revenue);

            // Update the chart with new data
            revenueChart.data.labels = labels;
            revenueChart.data.datasets[0].data = revenueData;
            revenueChart.update();
        })
        .catch(error => console.error('Error fetching revenue data:', error));
}

// Initial load for daily revenue data
fetchRevenueData('daily');
</script>