<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Save & Evolve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 w-full max-w-md shadow-2xl">
        <h1 class="text-2xl font-black text-center text-indigo-600 mb-2">Daftar Akun 🚀</h1>
        <p class="text-xs text-center text-slate-400 font-medium mb-6">Mulai petualangan menabungmu hari ini!</p>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-600 text-xs p-3.5 rounded-2xl mb-4 font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-600">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Email</label>
                <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-600">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-600">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-600">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3.5 rounded-2xl transition shadow-lg shadow-indigo-500/25">Daftar & Dapatkan Pet 🥚</button>
        </form>

        <p class="text-xs text-center text-slate-500 mt-5 font-medium">Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600 font-bold hover:underline">Login di sini</a></p>
    </div>
</body>
</html>