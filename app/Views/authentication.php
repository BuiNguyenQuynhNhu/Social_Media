<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('CSS/authentication.css') ?>">
</head>

<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                
                <form action="<?= base_url('login') ?>" method="post" class="sign-in-form">
                    <h2 class="title">Sign In</h2>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div style="color: red; margin-bottom: 10px;">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email" required />
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required />
                    </div>

                    <input type="submit" value="Login" class="btn solid" />

                    </form>

                <form action="<?= base_url('register') ?>" method="post" class="sign-up-form">
                    <h2 class="title">Sign Up</h2>
                    
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="firstname" placeholder="First name" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="lastname" placeholder="Last name" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-calendar"></i>
                        <input type="date" name="birth" required/>
                    </div>
                    
                    <div class="input-field">
                        <i class="fas fa-venus-mars"></i>
                        <select name="gender" style="background: none; border: none; outline: none; font-weight: 600; color: #333; width: 100%; padding: 0 10px;">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required/>
                    </div>

                    <input type="submit" value="Sign Up" class="btn solid" />
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>New here?</h3>
                    <p>Create new account to join with us.</p>
                    <button class="btn transparent" id="sign-up-btn">Sign Up</button>
                </div>
                <img src="<?= base_url('ICON/register.svg') ?>" class="image" alt="">
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us?</h3>
                    <p>Login to continue conservation.</p>
                    <button class="btn transparent" id="sign-in-btn">Sign In</button>
                </div>
                <img src="<?= base_url('ICON/login.svg') ?>" class="image" alt="">
            </div>
        </div>
    </div>

    <script src="<?= base_url('JS/authentication.js') ?>"></script>
</body>
</html>