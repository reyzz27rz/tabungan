<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true',
    openPetModal: false,
    openUserModal: false,
    openWithdrawModal: false
}" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save & Evolve — Pro Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } }
        }
    </script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } [x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#f8fafc] dark:bg-[#090d16] text-slate-900 dark:text-slate-100 transition-colors duration-300 min-h-screen flex flex-col justify-between antialiased selection:bg-indigo-500 selection:text-white">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 bg-teal-500/10 dark:bg-teal-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto p-4 md:p-8 w-full flex flex-col min-h-screen justify-between gap-6">
        
        <!-- HEADER -->
        <header class="flex justify-between items-center bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl p-4 md:p-5 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-800/80 transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-tr from-indigo-600 via-indigo-500 to-teal-400 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/25">SE</div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-base md:text-lg font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>Halo, {{ $user->name }}</span>
                            <button @click="openUserModal = true" class="text-slate-400 hover:text-indigo-500 text-xs transition p-1">✏️</button>
                        </h1>
                        <span class="hidden sm:inline-block bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase border border-indigo-100 dark:border-indigo-500/20">Active Saver</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Platform Menabung Cerdas & Interaktif</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-amber-400 transition border border-slate-200 dark:border-slate-700/50 shadow-sm">
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-100 dark:border-rose-500/20">Keluar</button>
                </form>
            </div>
        </header>

        <!-- ALERT NOTIFIKASI -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold text-xs md:text-sm p-4 rounded-2xl shadow-xl text-center border border-emerald-400/30">
                ✨ {{ session('success') }}
            </div>
        @endif
        @if($errors->has('withdraw_error'))
            <div x-data="{ show: true }" x-show="show" class="bg-gradient-to-r from-rose-600 to-red-600 text-white font-semibold text-xs md:text-sm p-4 rounded-2xl shadow-xl text-center border border-rose-400/30">
                ⚠️ {{ $errors->first('withdraw_error') }}
            </div>
        @endif

        <!-- MAIN GRID -->
        <main class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- LEFT COLUMN: PET & STATS -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                
                <!-- KARTU PET -->
                <div class="relative bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 text-white p-7 rounded-3xl border border-indigo-500/20 shadow-2xl overflow-hidden flex flex-col justify-between group">
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">Companion Status</span>
                            <div class="flex items-center gap-2 mt-2">
                                <h2 class="text-2xl font-black tracking-tight text-white">{{ $pet->pet_name }}</h2>
                                <button @click="openPetModal = true" class="text-xs bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg text-indigo-300 transition">✏️ Edit</button>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block font-medium">Evolusi Stage</span>
                            <span class="text-base font-extrabold text-teal-400">#{{ $pet->evolution_stage }} / 10</span>
                        </div>
                    </div>

                    <div class="my-8 flex flex-col items-center justify-center relative z-10">
                        <div class="w-36 h-36 bg-white/5 backdrop-blur-md rounded-full border border-white/10 flex items-center justify-center shadow-inner relative group-hover:scale-105 transition duration-500">
                            @switch($pet->evolution_stage)
                                @case(1) <span class="text-7xl animate-bounce">🥚</span> @break
                                @case(2) <span class="text-7xl animate-bounce">🐣</span> @break
                                @case(3) <span class="text-7xl animate-pulse">🦖</span> @break
                                @case(4) <span class="text-7xl animate-pulse">🧪</span> @break
                                @case(5) <span class="text-7xl animate-bounce">🐤</span> @break
                                @case(6) <span class="text-7xl animate-pulse">🐢</span> @break
                                @case(7) <span class="text-7xl animate-pulse">🐺</span> @break
                                @case(8) <span class="text-7xl animate-bounce">🐅</span> @break
                                @case(9) <span class="text-7xl animate-bounce">🐉</span> @break
                                @case(10) <span class="text-7xl animate-bounce">👾</span> @break
                            @endswitch
                        </div>
                    </div>

                    <!-- Progress Bar XP -->
                    <div class="relative z-10 space-y-2 bg-slate-900/60 p-4 rounded-2xl border border-white/5">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-300">XP Progress (Level {{ $user->level }} / 10)</span>
                            <span class="text-teal-400">{{ $user->level < 10 ? ($user->current_xp % 100) . ' / 100 XP' : 'MAX XP' }}</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5">
                            <div class="bg-gradient-to-r from-teal-400 to-indigo-500 h-full rounded-full transition-all duration-700" style="width: {{ $user->level >= 10 ? 100 : min(($user->current_xp % 100), 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- ACHIEVEMENTS SECTION -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-xl">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">🏆 Lencana Achievements</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($allAchievements as $ach)
                            @php $isUnlocked = in_array($ach->id, $unlockedAchievementIds); @endphp
                            <div class="p-3 rounded-2xl border text-center transition flex flex-col items-center justify-center {{ $isUnlocked ? 'bg-indigo-50/50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-500/30' : 'bg-slate-50 dark:bg-slate-950 border-slate-200/50 dark:border-slate-800 grayscale opacity-50' }}">
                                <span class="text-3xl mb-1">{{ $ach->badge_icon }}</span>
                                <span class="text-xs font-extrabold text-slate-900 dark:text-white block">{{ $ach->title }}</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">{{ $ach->description }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: SALDO & ACTION FORM -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Saldo Tabungan</span>
                            <button @click="openWithdrawModal = true" class="text-xs bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold px-3 py-1 rounded-full border border-rose-500/20 hover:bg-rose-500/20 transition">
                                💸 Tarik Saldo
                            </button>
                        </div>
                        <div class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-6">
                            Rp {{ number_format($user->total_savings, 0, ',', '.') }}
                        </div>

                        <!-- FORM MENABUNG -->
                        <form action="{{ route('saving.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">Nominal Setoran (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold text-sm">Rp</span>
                                    <input type="number" name="amount" required min="1000" placeholder="Contoh: 50000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl pl-12 pr-4 py-3.5 text-slate-900 dark:text-white font-bold text-sm focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">Catatan (Opsional)</label>
                                <input type="text" name="note" placeholder="Misal: Sisa uang jajan" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3.5 text-slate-900 dark:text-white font-medium text-sm focus:outline-none focus:border-indigo-500">
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-teal-500 hover:from-indigo-700 hover:to-teal-600 text-white font-black py-4 rounded-2xl transition shadow-xl shadow-indigo-500/25 flex items-center justify-center space-x-2 text-sm">
                                <span>Simpan & Beri Makan Pet 🚀</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </main>

        <!-- FOOTER: RIWAYAT MENABUNG & PENARIKAN -->
        <footer class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-xl grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-3">💵 Riwayat Menabung</h3>
                <div class="space-y-2">
                    @forelse($savings as $s)
                        <div class="flex justify-between items-center p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/50 dark:border-slate-800/80 text-xs">
                            <span class="font-extrabold text-emerald-500">+Rp {{ number_format($s->amount, 0, ',', '.') }}</span>
                            <span class="text-slate-400">{{ $s->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada riwayat menabung.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-3">💸 Riwayat Penarikan</h3>
                <div class="space-y-2">
                    @forelse($withdrawals as $w)
                        <div class="flex justify-between items-center p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/50 dark:border-slate-800/80 text-xs">
                            <span class="font-extrabold text-rose-500">-Rp {{ number_format($w->amount, 0, ',', '.') }}</span>
                            <span class="text-slate-400">{{ $w->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada penarikan saldo.</p>
                    @endforelse
                </div>
            </div>
        </footer>

    </div>

    <!-- MODAL PENARIKAN TABUNGAN -->
    <div x-show="openWithdrawModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4">
        <div @click.away="openWithdrawModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Tarik Tabungan 💸</h3>
            <p class="text-xs text-slate-400 mb-4">Saldo tersedia: <strong class="text-emerald-500">Rp {{ number_format($user->total_savings, 0, ',', '.') }}</strong></p>
            
            <form action="{{ route('withdraw.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Nominal Penarikan (Rp)</label>
                    <input type="number" name="amount" min="1000" max="{{ $user->total_savings }}" required placeholder="Contoh: 20000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">Alasan Penarikan (Opsional)</label>
                    <input type="text" name="note" placeholder="Misal: Beli buku kuliah" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-rose-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="openWithdrawModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-500/30">Tarik Uang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT NAMA PET -->
    <div x-show="openPetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4">
        <div @click.away="openPetModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Ganti Nama Pet 🐾</h3>
            <form action="{{ route('pet.update-name') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="pet_name" value="{{ $pet->pet_name }}" required max="50" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-indigo-500">
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="openPetModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT NAMA USER -->
    <div x-show="openUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4">
        <div @click.away="openUserModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Ganti Nama Profil 👤</h3>
            <form action="{{ route('user.update-name') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="name" value="{{ $user->name }}" required max="255" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-indigo-500">
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="openUserModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>