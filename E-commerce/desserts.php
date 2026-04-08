<?php include('includes/header.php'); ?>

<div class="bg-light py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Sweet <span style="color:var(--primary)">Desserts</span></h1>
        <p class="text-muted">Daily pastries and confections baked fresh every morning.</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Dessert 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="row g-0">
                        <div class="col-4">
                            <img src="https://placehold.co/300x400?text=Macarons" class="img-fluid rounded-start h-100" style="object-fit: cover;">
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <h5 class="fw-bold">French Macarons</h5>
                                <p class="small text-muted">Box of 12 assorted flavors.</p>
                                <p class="price-tag mb-0">$22.00</p>
                                <a href="#" class="btn btn-link p-0 text-dark fw-bold">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dessert 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="row g-0">
                        <div class="col-4">
                            <img src="https://placehold.co/300x400?text=Donuts" class="img-fluid rounded-start h-100" style="object-fit: cover;">
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <h5 class="fw-bold">Glazed Donuts</h5>
                                <p class="small text-muted">Classic melt-in-your-mouth.</p>
                                <p class="price-tag mb-0">$12.00</p>
                                <a href="#" class="btn btn-link p-0 text-dark fw-bold">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>