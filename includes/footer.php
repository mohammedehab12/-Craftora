</main>

<footer class="site-footer bg-dark text-light pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-cube me-2"></i>Craftora</h5>
                <p class="text-secondary small mb-0">
                    Handmade goods, crafted with care. Discover unique pieces for your home and everyday life.
                </p>
            </div>

            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>index.php" class="footer-link">Home</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>products.php" class="footer-link">Products</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>about.php" class="footer-link">About</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php" class="footer-link">Contact</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Get in Touch</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i>support@craftora.com</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2"></i>+20 100 000 0000</li>
                </ul>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-light"><i class="fa-brands fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-light"><i class="fa-brands fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-light"><i class="fa-brands fa-twitter fa-lg"></i></a>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <p class="text-center text-secondary small mb-0">
            &copy; <?php echo date('Y'); ?> Craftora. All rights reserved.
        </p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Site JS -->
<script src="<?php echo BASE_URL; ?>js/main.js"></script>

<?php if (!empty($extraScripts)) echo $extraScripts; ?>

</body>
</html>
