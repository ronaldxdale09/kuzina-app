<!-- At the end of your body tag, before closing </body> -->

<!-- jQuery (load first since other scripts depend on it) -->
<script src="assets/js/jquery-3.6.0.min.js"></script>

<!-- Core scripts -->
<script src="assets/js/bootstrap.bundle.min.js" defer></script>

<!-- Icons and theme -->
<script src="assets/js/lord-icon-2.1.0.js" defer></script>
<script src="assets/js/feather.min.js" defer></script>

<!-- Slider scripts (only load if slick slider exists on page) -->
<?php if(strpos(file_get_contents(__FILE__), 'slick-slider') !== false): ?>
    <script src="assets/js/slick.js" defer></script>
    <script src="assets/js/slick.min.js" defer></script>
    <script src="assets/js/slick-custom.js" defer></script>
<?php endif; ?>

<!-- Theme and custom scripts -->
<script src="assets/js/theme-setting.js" defer></script>
<script src="assets/js/script.js" defer></script>

<!-- Modals -->
<?php include 'modal/modal.dev.php'?>