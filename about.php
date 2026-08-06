<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Craftora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">About Craftora</h1>
                    <p class="lead mb-4">
                        Craftora is a marketplace for handmade products, connecting talented local artisans with customers who value quality craftsmanship.
                    </p>
                    <p class="mb-4">
                        We believe every handmade product tells a story. By connecting skilled artisans with conscious consumers, we create a marketplace where craftsmanship meets community.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="images/hero-image.jpg" alt="About Craftora" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Mission</h2>
                <p class="lead text-muted">Building a marketplace for handmade quality, one craft at a time</p>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-hands-helping fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Support Artisans</h4>
                        <p class="text-muted">
                            Empower local craftspeople and small business owners by providing a platform to showcase their unique, handmade products.
                        </p>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-gem fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Quality Craftsmanship</h4>
                        <p class="text-muted">
                            Every product is carefully made and curated to ensure high quality and authenticity you can trust.
                        </p>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-leaf fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Promote Sustainability</h4>
                        <p class="text-muted">
                            Encourage sustainable, handmade products over mass-produced items, reducing environmental impact.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Values</h2>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i> Transparency</h5>
                            <p class="text-muted mb-0">
                                We're upfront about our products, pricing, and where every item comes from.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i> Quality</h5>
                            <p class="text-muted mb-0">
                                Every product is carefully curated to ensure high quality and authenticity, supporting skilled artisans.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i> Community</h5>
                            <p class="text-muted mb-0">
                                Building connections between makers and buyers to create a supportive ecosystem.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i> Craftsmanship</h5>
                            <p class="text-muted mb-0">
                                Celebrating the skill and care that goes into every handmade product we sell.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Craftora So Far</h2>
            </div>

            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="impact-stat">
                        <h2 class="display-4 text-primary fw-bold">50+</h2>
                        <p class="text-muted">Artisans Supported</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="impact-stat">
                        <h2 class="display-4 text-primary fw-bold">1,000+</h2>
                        <p class="text-muted">Products Sold</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="impact-stat">
                        <h2 class="display-4 text-primary fw-bold">10+</h2>
                        <p class="text-muted">Product Categories</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Discover Handmade Quality</h2>
            <p class="lead mb-4">Browse our collection of authentic, handcrafted products</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="products.php" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
                <a href="contact.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
