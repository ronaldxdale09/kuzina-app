<!-- Balance Card -->
<div class="col-12">
    <div class="balance-card order-box theme-box">
        <div class="balance-content">
            <div class="balance-left">
                <span class="icon">
                    <i class='bx bx-money'></i>
                </span>
                <div class="order-content">
                    <?php
                  $rider_id = $_COOKIE['rider_id'];
                  // Get rider balance
                  $balance_stmt = $conn->prepare("SELECT balance FROM delivery_riders WHERE rider_id = ?");
                  $balance_stmt->bind_param("i", $rider_id);
                  $balance_stmt->execute();
                  $balance_result = $balance_stmt->get_result();
                  $balance_data = $balance_result->fetch_assoc();
                  $current_balance = $balance_data['balance'] ?? 0;
                    ?>
                    <h2>₱<?php echo number_format($current_balance, 2); ?></h2>
                    <span>Available Balance</span>
                </div>
            </div>
            <div class="balance-right">
                <a href="withdrawals.php" class="withdraw-btn">
                    <i class='bx bx-money-withdraw'></i>
                    Withdraw
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.balance-card {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    width: 100%;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.balance-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.balance-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.balance-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(80, 33, 33, 0.1);
    border-radius: 8px;
    flex-shrink: 0;
}

.balance-icon i {
    font-size: 24px;
    color: #502121;
}

.balance-details h2 {
    font-size: 24px;
    color: #502121;
    margin: 0 0 5px 0;
    font-weight: 600;
}

.balance-details span {
    font-size: 14px;
    color: #666;
}

.withdraw-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #502121;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.withdraw-btn:hover {
    background: #632929;
    color: white;
}

@media (max-width: 576px) {
    .balance-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .balance-action {
        display: flex;
        justify-content: center;
    }

    .withdraw-btn {
        width: 100%;
        justify-content: center;
    }
    
    .balance-details h2 {
        font-size: 22px;
    }
}
</style>