<?php
require_once __DIR__ . '/config.php';

$pageTitle  = 'About Us - Craftora';
$activePage = 'about';
include __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container" style="max-width: 800px;">
        <h1 class="section-title">About Craftora</h1>

        <img src="images/hero-image1.jpg" class="img-fluid rounded-4 mb-4" alt="Craftora workshop">

        <p class="text-muted">
            Craftora was founded with a simple idea: to bring the work of skilled artisans directly to people who
            appreciate quality, character, and craftsmanship. Every product in our shop is handmade — no mass
            production, no shortcuts.
        </p>
        <p class="text-muted">
            We partner with small workshops and independent makers who specialize in textiles, ceramics, leatherwork,
            and natural body care. Each piece is crafted in small batches, meaning what you find today may not be
            available tomorrow.
        </p>
        <p class="text-muted">
            Our mission is to support sustainable, small-scale production while giving our customers access to
            beautiful, functional goods for their homes and everyday lives.
        </p>

        <div class="row text-center mt-5 g-4">
            <div class="col-md-4">
                <i class="fa-solid fa-hand-holding-heart fa-2x mb-2" style="color: var(--clr-accent);"></i>
                <h6 class="fw-bold">Handmade</h6>
                <p class="text-muted small">Every item is crafted by hand, not machine.</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-leaf fa-2x mb-2" style="color: var(--clr-accent);"></i>
                <h6 class="fw-bold">Sustainable</h6>
                <p class="text-muted small">Natural materials, low-waste production.</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-people-group fa-2x mb-2" style="color: var(--clr-accent);"></i>
                <h6 class="fw-bold">Community</h6>
                <p class="text-muted small">Supporting independent artisans directly.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
