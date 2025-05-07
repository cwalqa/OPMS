<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="{{ asset('css/chpass.css') }}">
</head>
<body>
    <div class="page">
        <div class="container">
            <form class="content" action="{{ route('change-password.post') }}" method="POST">
                @csrf

                <div class="lock-image">
                    <svg width="60" height="60" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="69.1606" cy="78.2663" rx="53.5" ry="66.5" transform="rotate(15.3101 69.1606 78.2663)" fill="#D9D9D9" />
                        <path d="M46.0855 71.5272L39.1408 58.5438C36.535 53.6723 38.3733 47.6107 43.2461 45.0072L59.4961 36.325C64.3669 33.7226 70.4252 35.5611 73.0281 40.4317L79.9688 53.4191" stroke="white" stroke-width="5" />
                        <rect x="35.8906" y="76.5552" width="63" height="54" rx="10" transform="rotate(-28.121 35.8906 76.5552)" fill="#ED9A1F" />
                        <circle cx="74.7485" cy="82.4342" r="6.5" transform="rotate(-28.121 74.7485 82.4342)" fill="black" />
                        <path d="M80.6709 94.5762L72.6582 79.583" stroke="black" stroke-width="5" stroke-linecap="round" />
                    </svg>
                </div>

                <h2 class="heading">Create a secure password</h2>

                <div class="input-container">
                    <div class="password-container">
                        <label class="password-label">New Password</label>
                        <input class="password-input" type="password" name="password" autocomplete="off" autofocus />
                    </div>
                    <span class="show-password">Show</span>
                </div>

                <div class="password-strength">
                    <small>Password strength</small>
                    <div class="progress-bar">
                        <div class="bar"></div>
                    </div>
                    <small class="password-strength-text"></small>
                </div>

                <div class="validation-list">
                    <span>Must contain at least</span>
                    <ul class="validation-items">
                        <li class="validation-item">
                            <span class="validation-item-dot-1"></span>
                            <span class="validation-item-check-1">&#10003;</span>
                            <span class="validation-item-text">8 letters</span>
                        </li>
                        <li class="validation-item">
                            <span class="validation-item-dot-2"></span>
                            <span class="validation-item-check-2">&#10003;</span>
                            <span class="validation-item-text">1 uppercase character</span>
                        </li>
                        <li class="validation-item">
                            <span class="validation-item-dot-3"></span>
                            <span class="validation-item-check-3">&#10003;</span>
                            <span class="validation-item-text">1 special character</span>
                        </li>
                    </ul>
                </div>

                <button type="submit" class="button">Continue</button>
            </form>
        </div>
    </div>

    <script>
        var bar = document.querySelector(".bar");
        var showPassword = document.querySelector(".show-password");
        var passwordInput = document.querySelector(".password-input");
        var passwordLabel = document.querySelector(".password-label");
        var inputContainer = document.querySelector(".input-container");
        var passwordContainer = document.querySelector(".password-container");

        var validationDotOne = document.querySelector(".validation-item-dot-1");
        var validationDotTwo = document.querySelector(".validation-item-dot-2");
        var validationCheckOne = document.querySelector(".validation-item-check-1");
        var validationCheckTwo = document.querySelector(".validation-item-check-2");
        var validationDotThree = document.querySelector(".validation-item-dot-3");
        var validationCheckThree = document.querySelector(".validation-item-check-3");
        var passwordStrengthText = document.querySelector(".password-strength-text");

        var format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;

        function hasUpperCase(string) {
            return string.toLowerCase() != string;
        }

        passwordContainer.addEventListener("click", function () {
            passwordInput.focus();
            passwordLabel.style.top = "5px";
            passwordInput.style.display = "block";
            passwordLabel.style.fontSize = "12px";
            passwordLabel.style.position = "absolute";
            passwordInput.style.position = "absolute";
            passwordInput.style.marginTop = "5px";
        });

        showPassword.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                showPassword.innerText = "Hide";
            } else {
                passwordInput.type = "password";
                showPassword.innerText = "Show";
            }
        });

        passwordInput.addEventListener("input", function () {
            const arr = [];

            if (passwordInput.value.length >= 8) {
                arr.push(1);
                validationDotOne.style.display = "none";
                validationCheckOne.style.display = "block";
            } else {
                validationDotOne.style.display = "block";
                validationCheckOne.style.display = "none";
            }

            if (hasUpperCase(passwordInput.value)) {
                arr.push(2);
                validationDotTwo.style.display = "none";
                validationCheckTwo.style.display = "block";
            } else {
                validationDotTwo.style.display = "block";
                validationCheckTwo.style.display = "none";
            }

            if (format.test(passwordInput.value)) {
                arr.push(3);
                validationDotThree.style.display = "none";
                validationCheckThree.style.display = "block";
            } else {
                validationDotThree.style.display = "block";
                validationCheckThree.style.display = "none";
            }

            switch (arr.length) {
                case 0:
                    bar.style.width = "0px";
                    passwordStrengthText.innerText = "";
                    break;
                case 1:
                    bar.style.width = "50px";
                    bar.style.background = "chocolate";
                    passwordStrengthText.innerText = "Weak";
                    break;
                case 2:
                    bar.style.width = "100px";
                    bar.style.background = "#d0ce3e";
                    passwordStrengthText.innerText = "Medium";
                    break;
                case 3:
                    bar.style.width = "120px";
                    bar.style.background = "#41ce55";
                    passwordStrengthText.innerText = "Excellent";
                    break;
                default:
                    break;
            }
        });
    </script>
</body>
</html>
