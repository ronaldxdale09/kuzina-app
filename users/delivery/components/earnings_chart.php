<!-- earnings_chart.php -->
<?php
function getEarningsData($riderId, $duration, $conn) {
    switch($duration) {
        case 'daily':
            $query = "SELECT 
                        DATE_FORMAT(earning_date, '%h:%i %p') as period,
                        SUM(amount) as earnings
                    FROM rider_earnings 
                    WHERE rider_id = ? 
                    AND DATE(earning_date) = CURDATE()
                    GROUP BY HOUR(earning_date)
                    ORDER BY earning_date";
            break;
            
        case 'weekly':
            $query = "SELECT 
                        DATE_FORMAT(earning_date, '%W') as period,
                        SUM(amount) as earnings
                    FROM rider_earnings 
                    WHERE rider_id = ? 
                    AND earning_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY DATE(earning_date)
                    ORDER BY earning_date";
            break;
            
        case 'monthly':
            $query = "SELECT 
                        DATE_FORMAT(earning_date, '%M %d') as period,
                        SUM(amount) as earnings
                    FROM rider_earnings 
                    WHERE rider_id = ? 
                    AND earning_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY DATE(earning_date)
                    ORDER BY earning_date";
            break;
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $riderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $chartData = [];
    while($row = $result->fetch_assoc()) {
        $chartData[] = $row;
    }
    
    // Get total earnings for the period
    $totalQuery = str_replace("DATE_FORMAT(earning_date, '%h:%i %p') as period,", "", $query);
    $totalQuery = str_replace("DATE_FORMAT(earning_date, '%W') as period,", "", $totalQuery);
    $totalQuery = str_replace("DATE_FORMAT(earning_date, '%M %d') as period,", "", $totalQuery);
    $totalQuery = preg_replace("/GROUP BY.*/", "", $totalQuery);
    
    $stmt = $conn->prepare($totalQuery);
    $stmt->bind_param("i", $riderId);
    $stmt->execute();
    $totalResult = $stmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    
    return [
        'chartData' => $chartData,
        'totalEarnings' => $totalRow['earnings'] ?? 0
    ];
}
?>

<section class="earnings-section">
    <div class="earnings-header">
        <h4>Earnings Overview</h4>
        <div class="earnings-filter">
            <select id="earnings-duration" class="custom-select">
                <option value="daily">Today</option>
                <option value="weekly">This Week</option>
                <option value="monthly">This Month</option>
            </select>
        </div>
    </div>
    <div class="earnings-total">
        <h3 id="total-earnings">₱0.00</h3>
        <div class="trend-indicator">
            <i class='bx bx-trending-up'></i>
            <span id="earnings-trend">0%</span>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="earningsChart"></canvas>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const earningsChartCtx = document.getElementById('earningsChart').getContext('2d');
    let earningsChart = new Chart(earningsChartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Earnings',
                data: [],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#28a745'
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
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value;
                        }
                    }
                }
            }
        }
    });

    // Fetch earnings data function
    function fetchEarningsData(duration) {
        fetch(`api/get_earnings.php?duration=${duration}`)
            .then(response => response.json())
            .then(data => {
                // Update total earnings
                document.getElementById('total-earnings').textContent = 
                    '₱' + data.totalEarnings.toFixed(2);

                // Calculate trend
                const lastTwoValues = data.chartData.slice(-2);
                if (lastTwoValues.length === 2) {
                    const trend = ((lastTwoValues[1].earnings - lastTwoValues[0].earnings) / 
                                 lastTwoValues[0].earnings * 100) || 0;
                    document.getElementById('earnings-trend').textContent = 
                        trend.toFixed(1) + '%';
                    
                    // Update trend icon color
                    const trendIndicator = document.querySelector('.trend-indicator');
                    if (trend > 0) {
                        trendIndicator.style.color = '#28a745';
                        trendIndicator.querySelector('i').className = 'bx bx-trending-up';
                    } else {
                        trendIndicator.style.color = '#dc3545';
                        trendIndicator.querySelector('i').className = 'bx bx-trending-down';
                    }
                }

                // Update chart
                earningsChart.data.labels = data.chartData.map(item => item.period);
                earningsChart.data.datasets[0].data = data.chartData.map(item => item.earnings);
                earningsChart.update();
            })
            .catch(error => console.error('Error fetching earnings data:', error));
    }

    // Duration change handler
    document.getElementById('earnings-duration').addEventListener('change', function() {
        fetchEarningsData(this.value);
    });

    // Initial load
    fetchEarningsData('daily');
});
</script>