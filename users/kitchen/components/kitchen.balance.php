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
                    // Get kitchen balance
                    $balance_stmt = $conn->prepare("SELECT balance FROM kitchens WHERE kitchen_id = ?");
                    $balance_stmt->bind_param("i", $kitchen_id);
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
    padding: 20px;
    width: 100%;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.balance-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.balance-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.balance-card .icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(80, 33, 33, 0.1);
    border-radius: 8px;
}

.balance-card .icon i {
    font-size: 28px;
    color: #502121;
}

.balance-card .order-content h2 {
    font-size: 28px;
    color: #502121;
    margin-bottom: 5px;
    font-weight: 600;
}

.balance-card .order-content span {
    font-size: 16px;
    color: #666;
}

.withdraw-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #502121;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.withdraw-btn:hover {
    background: #632929;
    color: white;
}
</style>