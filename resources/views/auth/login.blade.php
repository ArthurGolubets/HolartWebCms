<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - {{ config('holart-cms.name', 'HolartCMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes backgroundMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        .animate-bg-move { animation: backgroundMove 20s linear infinite; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .animate-shake { animation: shake 0.5s ease; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-500 via-purple-600 to-secondary-500 flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Animated Background Pattern -->
    <div class="absolute inset-0 -top-1/2 -left-1/2 w-[200%] h-[200%] opacity-10 animate-bg-move"
         style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 50px 50px;"></div>

    <div class="relative z-10 w-full max-w-md">
        <!-- Login Container -->
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl p-10 animate-slide-up">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-primary-500 to-secondary-500 bg-clip-text text-transparent mb-2">
                    {{ config('holart-cms.name', 'HolartCMS') }}
                </h1>
                <p class="text-gray-600 text-sm">Панель администратора</p>
            </div>

            <!-- Error Alert -->
            <div id="error-alert" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm animate-shake"></div>

            <!-- Login Form -->
            <form id="login-form" method="POST" action="{{ route('holart-cms.login.post') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           required
                           autofocus
                           placeholder="your@email.com"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-100 transition-all outline-none">
                    <span id="email-error" class="text-red-500 text-sm mt-1 hidden"></span>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Пароль</label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           placeholder="Введите пароль"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-100 transition-all outline-none">
                    <span id="password-error" class="text-red-500 text-sm mt-1 hidden"></span>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="remember" name="remember" class="w-5 h-5 text-primary-500 border-gray-300 rounded focus:ring-primary-500 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600">Запомнить меня</span>
                    </label>
                    <a href="{{ route('holart-cms.password.request') }}" class="text-sm font-medium text-primary-500 hover:text-secondary-500 transition">
                        Забыли пароль?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        id="login-btn"
                        class="relative w-full py-3 px-4 bg-gradient-to-r from-primary-500 to-secondary-500 text-white font-semibold rounded-xl hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary-200 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span id="btn-text">Войти</span>
                    <div id="loader" class="hidden absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-6 text-white/90 text-sm">
            &copy; {{ date('Y') }} {{ config('holart-cms.name', 'HolartCMS') }}. Все права защищены.
        </p>
    </div>

    <script>
        const form = document.getElementById('login-form');
        const submitBtn = document.getElementById('login-btn');
        const btnText = document.getElementById('btn-text');
        const loader = document.getElementById('loader');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear errors
            document.getElementById('error-alert').classList.add('hidden');
            document.getElementById('email-error').classList.add('hidden');
            document.getElementById('password-error').classList.add('hidden');

            // Show loading
            submitBtn.disabled = true;
            btnText.classList.add('opacity-0');
            loader.classList.remove('hidden');

            const formData = new FormData(e.target);

            try {
                const response = await fetch(e.target.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = data.redirect;
                } else {
                    // Hide loading
                    submitBtn.disabled = false;
                    btnText.classList.remove('opacity-0');
                    loader.classList.add('hidden');

                    if (data.errors) {
                        if (data.errors.email) {
                            const emailError = document.getElementById('email-error');
                            emailError.textContent = data.errors.email[0];
                            emailError.classList.remove('hidden');
                        }
                        if (data.errors.password) {
                            const passwordError = document.getElementById('password-error');
                            passwordError.textContent = data.errors.password[0];
                            passwordError.classList.remove('hidden');
                        }
                    } else if (data.message) {
                        const alert = document.getElementById('error-alert');
                        alert.textContent = data.message;
                        alert.classList.remove('hidden');
                    }
                }
            } catch (error) {
                submitBtn.disabled = false;
                btnText.classList.remove('opacity-0');
                loader.classList.add('hidden');

                const alert = document.getElementById('error-alert');
                alert.textContent = 'Произошла ошибка. Пожалуйста, попробуйте снова.';
                alert.classList.remove('hidden');
            }
        });
    </script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            100: '#ebf0fe',
                            200: '#d6e0fd',
                            500: '#667eea',
                            600: '#5568d3',
                        },
                        secondary: {
                            500: '#a855f7',
                            600: '#9333ea',
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>
