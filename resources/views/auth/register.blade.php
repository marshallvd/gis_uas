<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Register</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="relative flex flex-col justify-center h-screen overflow-hidden">
        <div class="w-full p-6 m-auto bg-white rounded-md shadow-xl ring-2 ring-gray-800/50 lg:max-w-xl  card w-150">
            <h1 class="text-3xl font-semibold  text-gray-700"><b>Register</b></h1>

            <form action="{{ route('register.save') }}" method="POST" class="space-y-4">
                @csrf
                @if ($errors->any())
                <div role="alert" class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <ul style="color:red">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <label class="input input-bordered flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                    <input name="name" type="text" placeholder="Name"  @error('name')is-invalid @enderror" />
                    
                </label>

                <label class="input input-bordered flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                    <input name="email" type="email" placeholder="Email Address"  />
                    
                </label>
                
                <label class="input input-bordered flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                    <input name="password" type="password" placeholder="Enter Password"  />
                    
                </label>
                <label class="input input-bordered flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                    <input name="password" type="password" placeholder="Confirm Password"  />
                    
                </label>

                <div>
                    <label class="label cursor-pointer">
                        <span class="label-text">Remember me</span>
                        <input name="remember" type="checkbox" checked="checked" id="customCheck" class="checkbox checkbox-primary" />
                    </label>
                </div>
                <div>
                    <button type="submit" class="btn btn-xs sm:btn-sm md:btn-md lg:btn-lg">Register</button>
                    
                </div>

                <div>
                    <span>Already have an account ?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 hover:underline">Login</a>
                    </span>
                </div>
                
            </form>
            
        </div>
    </div>

    <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
    <script src="/js/register.js"></script>

    <script>
//         document.getElementById('registerForm').addEventListener('submit', function(event) {
//     event.preventDefault();
    
//     let isValid = true;
//     const form = document.getElementById('registerForm')

//     const username = document.getElementById('username').value.trim();
//     const email = document.getElementById('email').value.trim();
//     const password = document.getElementById('password').value.trim();
//     // const confirmPassword = document.getElementById('confirmPassword').value.trim();

//     const usernameError = document.getElementById('usernameError');
//     const emailError = document.getElementById('emailError');
//     const passwordError = document.getElementById('passwordError');
//     // const confirmPasswordError = document.getElementById('confirmPasswordError');

//     usernameError.style.display = 'none';
//     emailError.style.display = 'none';
//     passwordError.style.display = 'none';
//     // confirmPasswordError.style.display = 'none';

//     if (username.length < 3) {
//         isValid = false;
//         usernameError.textContent = 'Username must be at least 3 characters long';
//         usernameError.style.display = 'block';
//     }

//     const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     if (!emailPattern.test(email)) {
//         isValid = false;
//         emailError.textContent = 'Invalid email address';
//         emailError.style.display = 'block';
//     }

//     if (password.length < 6) {
//         isValid = false;
//         passwordError.textContent = 'Password must be at least 6 characters long';
//         passwordError.style.display = 'block';
//     }

//     // if (password !== confirmPassword) {
//     //     isValid = false;
//     //     confirmPasswordError.textContent = 'Passwords do not match';
//     //     confirmPasswordError.style.display = 'block';
//     // }

//     if (isValid) {
//         // alert('Form submitted successfully!');
//         // Here you can add code to send the form data to the server
//         const formData = new FormData(form);
//         const jsonObject = {};

//         formData.forEach((value, key) => {
//             jsonObject[key] = value
//         });
//         console.log(jsonObject)

//         const url = "https://gisapis.manpits.xyz/api/register"

//         axios.post(url, jsonObject)
//         .then(response =>{
//             alert(response.data.meta.message)
//             // alert('Register Form submitted successfully!');
//             console.log(response)
//             window.location.href = '/login.html'
//         })
//         .catch(error => {
//             alert('Failed to submit register form. Please try again later.');
//             console.log(error)
//         })

//     }
// });
    </script>
</body>
</html>