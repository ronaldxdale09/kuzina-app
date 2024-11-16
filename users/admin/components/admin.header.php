<div class="admin-header">
    <style>
    .admin-header {
        padding: 1.5rem 1rem;
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        border-bottom: 1px solid #eaeaea;
        margin-bottom: 1.5rem;
    }

    .admin-header-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .admin-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .admin-subtitle {
        font-size: 0.9rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .admin-badge {
        background-color: #4CAF50;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        margin-left: 0.5rem;
    }

    .time-badge {
        background-color: #f8f9fa;
        color: #666;
        padding: 0.25rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    @media (max-width: 768px) {
        .admin-header {
            padding: 1rem;
        }

        .admin-title {
            font-size: 1.25rem;
        }
    }
    </style>

    <div class="admin-header-content">
        <div class="admin-title">
            <i class='bx bx-grid-alt'></i>
            Admin Dashboard
            <span class="admin-badge">KUZINA</span>
        </div>
        <div class="admin-subtitle">
            <span class="time-badge">
                <i class='bx bx-calendar'></i>
                <span id="currentDate">Today's Overview</span>
            </span>
        </div>
    </div>
</div>

<script>
// Update current date
function updateDate() {
    const date = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    document.getElementById('currentDate').textContent = date.toLocaleDateString('en-US', options);
}
updateDate();
</script>