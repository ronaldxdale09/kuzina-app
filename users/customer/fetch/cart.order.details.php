<section class="order-detail pt-1">
    <h3 class="title-2">Order Details</h3>

    <!-- Detail list Start -->
    <ul>
        <li>
            <span>Bag total</span>
            <span>₱<span id="bag-total">0.00</span></span> <!-- Changed from <label> to <span> -->
        </li>

        <li>
            <span>Coupon Discount</span>
            <div style="display: flex; align-items: center;">
                <input type="text" id="coupon-code" placeholder="Enter coupon code" class="font-danger"
                    style="width: 150px; margin-right: 10px;">
                <button id="apply-coupon-btn" style="padding: 5px 10px;">Apply</button>
                <span id="coupon-discount" style="margin-left: 10px;">₱0.00</span>
            </div>
        </li>

        <li>
            <span>Delivery</span>
            <span>₱<span id="delivery-fee">50.00</span></span>
        </li>

        <li>
            <span>Total Amount</span>
            <span>₱<span id="total-amount">0.00</span></span> <!-- Added initial value of 0.00 -->
        </li>
    </ul>
    <!-- Detail list End -->
</section>
