<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Resto Kay-Y</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased flex items-center justify-center min-h-screen">
    
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
        <!-- Logo / Tit -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-amber-600">🍽️ RESTO KAY-Y</h1>
            <p class="text-gray-500 text-sm mt-1">Konekte pou w ka jere kòmand yo</p>
        </div>

        <!-- Afiche erè si genyen -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Fòm Login nan -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Imèl -->
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-2">Imèl (Email)</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm"
                    placeholder="pa egzanp: admin@resto.com">
            </div>

            <!-- Modpas -->
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-2">Modpas (Password)</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm"
                    placeholder="••••••••">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <span class="ml-2 text-gray-600">Bouke sonje m</span>
                </label>
            </div>

            <!-- Bouton Submit -->
            <button type="submit"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md">
                Konekte (Login)
            </button>
        </form>
    </div>

</body>
</html>