<!DOCTYPE html>
<html lang= "en">
<head>
    <meta charset= "UTF-8">
    <meta name= "viewport" content= "width = device-width, initial-scale= 1.0">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel= "stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel= "stylesheet" href="<?=base_url('CSS/authentication.css') ?>">
</head>

<body>
    <div class= 'container'>
        <div class= 'forms-container'>
            <div class= 'signin-signup'>
                <form action= "" class= "sign-in">
                    <h2 class = 'title'> Sign In </h2>
                    <div class = 'input-field'>
                        <i class = 'fas fa-user'></i>
                        <input type= 'text' placeholder= 'Email'/>
                    </div>

                    <div class = 'input-field'>
                        <i class= 'fas fa-lock'></i>
                        <input type= 'password' placeholder="Password" />
                    </div>

                    <!-- <p class= 'social-text'> Or Sign in with social platform </p>
                    <div class= 'social-media'>
                        <a href= "#" class= 'social-icon'>
                            <i class = 'fab fa-facebook-f'></i>
                        </a>
                        <a href= "#" class= 'social-icon'>
                            <i class = 'fab fa-google'></i>
                        </a>
                        <a href= "#" class= 'social-icon'>
                            <i class = 'fab fa-linkedin-in'></i>
                        </a>
                    </div> -->
                </form>
                <form action= "" class= 'sign-up'>
                    <h2 class= 'title'>Sign Up </h2>
                    <div class= 'input-field'>
                        <i class= 'fas fa-user'></i>
                        <input type= 'text' placeholder="First name"/>
                    </div>
                    <div class= 'input-field'>
                        <i class= 'fas fa-user'></i>
                        <input type= 'text' placeholder="Last name"/>
                    </div>
                    <div class= 'input-field'>
                        <i class= 'fas fa-envelope'></i>
                        <input type= 'email' placeholder="Email"/>
                    </div>
                    <div class= 'input-field'>
                        <i class= 'fas fa-calender'></i>
                        <input type= 'date' placeholder="Birth"/>
                    </div>
                    <div class= 'input-field'>
                        <i class= 'fas fa-lock'></i>
                        <input type= 'password' placeholder="Password"/>
                    </div>
                    <div class= 'input-field'>
                        <i class= 'fas fa-user'></i>
                        <input type= 'password' placeholder="Re-enterPassword"/>
                    </div>
                    <!-- <p class= 'social-text'> Or Sign up with social platform </p>
                    <div class= 'social-media'>
                        <a href= "#" class= 'social-icon'>
                            <i class = 'fab fa-facebook-f'></i>
                        </a>
                        <a href= "#" class= 'social-icon'>
                            <i class = 'fab fa-google'></i>
                        </a>
                        <a href= "#" class= 'social-icon'>
                            <i class = 'fab fa-linkedin-in'></i>
                        </a>
                    </div> -->
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
            <!-- <img src="../../public/ICON/logo.png" class="image" alt=""> -->
        </div>

        <div class="panel right-panel">
            <div class="content">
                <h3>One of us?</h3>
                <p>Login to continue conservation.</p>
                <button class="btn transparent" id="sign-in-btn">Sign In</button>
            </div>
            <!-- <img src="../../public/ICON/logo.png" class="image" alt=""> -->
        </div>
      </div>
    </div>
    </div>
    <script src= "../../public/JS/authentication.js"></script>
</body>
</html>