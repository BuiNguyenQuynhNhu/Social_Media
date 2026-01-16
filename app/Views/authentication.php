<?php
    $isSignUp = session()->getFlashdata('error_register');
    $containerClass = $isSignUp ? 'sign-up-mode' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link rel="icon" href="<?= base_url('ICON/logo.png') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('CSS/authentication.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
</head>

<body>
    <div class="container <?= $containerClass ?>">   

        <div class="forms-container">
            <div class="signin-signup">
                <form action="<?= base_url('login') ?>" method="post" class="sign-in-form">
                    <h2 class="title">Sign In</h2>

                    <?= csrf_field() ?>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert-error">
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
                    
                    <p class="social-text">Or Sign in with social platforms</p>
                    <div class="social-media">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-google"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </form>

                <form action="<?= base_url('register') ?>" method="post" class="sign-up-form">
                    <h2 class="title">Sign Up</h2>

                    <?= csrf_field() ?>
                    
                    <?php if(session()->getFlashdata('error_register')): ?>
                        <div class="alert-error">
                            <?= session()->getFlashdata('error_register') ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="firstname" placeholder="First Name" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="lastname" placeholder="Last Name" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required/>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-field small">
                            <i class="fas fa-calendar"></i>
                            <input type="date" name="birth" required/>
                        </div>
                        <div class="input-field small">
                            <i class="fas fa-venus-mars"></i>
                            <select name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required/>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="repassword" placeholder="rePassword" required/>
                    </div>

                    <input type="submit" value="Sign Up" class="btn solid" />
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>New here?</h3>
                    <p>Create new account to join with us and explore the community.</p>
                    <button class="btn transparent" id="sign-up-btn">Sign Up</button> 
                </div>
                <img src="<?= base_url('ICON/logo.png') ?>" class="image" alt="">
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us?</h3>
                    <p>Login to continue your journey and connect with friends.</p>
                    <button class="btn transparent" id="sign-in-btn">Sign In</button>
                </div>
                <img src="<?= base_url('ICON/logo.png') ?>" class="image" alt="">
            </div>
        </div>
    </div>

    <script src="<?= base_url('JS/authentication.js') ?>"></script>
</body>
</html>