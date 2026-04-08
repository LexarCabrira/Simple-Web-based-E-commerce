<?php include('includes/header.php'); ?>

<style>
    :root {
        --silver-focus: #9da1a5;
    }
    body { 
        background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        min-height: 100vh;
    }
    
    .auth-container {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 50px 40px;
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        width: 100%;
        max-width: 420px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .auth-header h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
    }

    .auth-header p {
        font-size: 0.9rem;
        color: #888;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #555;
        margin-left: 5px;
    }

    .form-control {
        border-radius: 12px;
        padding: 14px 20px;
        border: 1px solid #e1e1e1;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: var(--silver-focus);
        box-shadow: 0 0 0 4px rgba(157, 161, 165, 0.1);
        outline: none;
    }

    .btn-auth {
        background-color: #1a1a1a;
        color: white;
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        margin-top: 15px;
        transition: transform 0.2s ease, background 0.3s ease;
    }

    .btn-auth:hover {
        background-color: #333;
        transform: translateY(-2px);
    }

    .auth-footer {
        text-align: center;
        margin-top: 30px;
        font-size: 0.9rem;
        color: #666;
    }

    .auth-footer a {
        color: var(--silver-focus);
        text-decoration: none;
        font-weight: 700;
    }

    .back-home {
        position: absolute;
        top: 20px;
        left: 20px;
    }
</style>

<div class="auth-container">
    <a href="index.php" class="back-home text-muted text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Back to Home
    </a>

    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Please enter your details to login</p>
        </div>
        
<!-- Find the form tag in your login.php and update it: -->
<form action="login_process.php" method="POST">
    <div class="mb-4">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="name@gmail.com" required>
    </div>

    <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>

    <button type="submit" name="login_btn" class="btn-auth shadow-sm">Sign In</button>
</form>

        <div class="auth-footer">
            New to Silverlocks? <a href="register.php">Create Account</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>