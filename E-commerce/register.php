<?php include('includes/header.php'); ?>

<style>
    :root { --silver-focus: #9da1a5; }
    body { 
        background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        min-height: 100vh;
    }
    
    .auth-container {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 0;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        padding: 50px;
        border-radius: 35px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 600px;
        border: 1px solid #fff;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .auth-header h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 2rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #777;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 18px;
        border: 1px solid #eee;
        background-color: #fcfcfc;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: var(--silver-focus);
        box-shadow: 0 0 0 4px rgba(157, 161, 165, 0.1);
        background-color: #fff;
    }

    .btn-auth {
        background-color: #1a1a1a;
        color: white;
        width: 100%;
        padding: 15px;
        border-radius: 15px;
        font-weight: 600;
        border: none;
        margin-top: 20px;
        letter-spacing: 1px;
    }

    .auth-footer {
        text-align: center;
        margin-top: 25px;
        font-size: 0.9rem;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Join Silverlocks</h2>
            <p class="text-muted">Create an account to start ordering sweets</p>
        </div>
        
        <form action="register_process.php" method="POST">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" placeholder="John Doe" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="0912 345 6789" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label">Shipping Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full address for delivery"></textarea>
                </div>
            </div>

            <button type="submit" class="btn-auth">Create My Account</button>
        </form>

        <div class="auth-footer">
            <span class="text-muted">Already have an account?</span> 
            <a href="login.php" class="text-dark fw-bold text-decoration-none ms-1">Login here</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>