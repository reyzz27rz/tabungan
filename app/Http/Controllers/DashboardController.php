<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saving;
use App\Models\Withdrawal;
use App\Models\UserPet;
use App\Models\Achievement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pet = UserPet::firstOrCreate(
            ['user_id' => $user->id],
            ['pet_name' => 'Mistery Egg 🥚', 'evolution_stage' => 1, 'pet_xp' => 0]
        );

        $savings = Saving::where('user_id', $user->id)->latest()->take(5)->get();
        $withdrawals = Withdrawal::where('user_id', $user->id)->latest()->take(5)->get();

        // Inisialisasi Master Data Achievement jika belum ada
        $this->seedAchievements();

        // Ambil Achievement beserta status unlock milik user
        $allAchievements = Achievement::all();
        $unlockedAchievementIds = $user->achievements()->pluck('achievement_id')->toArray();

        return view('dashboard', compact('user', 'pet', 'savings', 'withdrawals', 'allAchievements', 'unlockedAchievementIds'));
    }

    public function storeSaving(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'note' => 'nullable|string|max:255'
        ]);

        $user = auth()->user();
        
        Saving::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'note' => $request->note ?? 'Menabung harian'
        ]);

        $user->total_savings += $request->amount;
        $earnedXp = intval($request->amount / 1000) * 5; 
        $user->current_xp += $earnedXp;
        $user->points += intval($request->amount / 2000);

        $calculatedLevel = intval($user->current_xp / 100) + 1;
        $user->level = min($calculatedLevel, 10);
        $user->save();

        // Update Pet
        $pet = UserPet::where('user_id', $user->id)->first();
        $pet->pet_xp += $earnedXp;

        $stages = [
            1  => ['stage' => 1,  'name' => 'Mistery Egg 🥚'],
            2  => ['stage' => 2,  'name' => 'Cracking Egg 🐣'],
            3  => ['stage' => 3,  'name' => 'Baby Dino 🦖'],
            4  => ['stage' => 4,  'name' => 'Cute Slime 🧪'],
            5  => ['stage' => 5,  'name' => 'Young Phoenix 🐤'],
            6  => ['stage' => 6,  'name' => 'Spike Turtle 🐢'],
            7  => ['stage' => 7,  'name' => 'Alpha Wolf 🐺'],
            8  => ['stage' => 8,  'name' => 'Shadow Tiger 🐅'],
            9  => ['stage' => 9,  'name' => 'Ancient Dragon 🐉'],
            10 => ['stage' => 10, 'name' => 'Godzilla Mythic 👾'],
        ];

        if (isset($stages[$user->level])) {
            $pet->evolution_stage = $stages[$user->level]['stage'];
            $pet->pet_name = $stages[$user->level]['name'];
        }
        $pet->save();

        // Cek Peningkatan Achievement
        $this->checkAchievements($user);

        return redirect()->back()->with('success', "Berhasil menabung! Kamu mendapat +{$earnedXp} XP & Poin!");
    }

    // FITUR PENARIKAN TABUNGAN
    public function storeWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'note' => 'nullable|string|max:255'
        ]);

        $user = auth()->user();

        // Cek ketersediaan saldo
        if ($request->amount > $user->total_savings) {
            return redirect()->back()->withErrors(['withdraw_error' => 'Saldo tabungan tidak mencukupi untuk penarikan ini!']);
        }

        // Catat Penarikan & Kurangi Saldo
        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'note' => $request->note ?? 'Penarikan tabungan'
        ]);

        $user->total_savings -= $request->amount;
        $user->save();

        return redirect()->back()->with('success', 'Penarikan saldo sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil!');
    }

    public function updatePetName(Request $request)
    {
        $request->validate(['pet_name' => 'required|string|max:50']);
        $pet = UserPet::where('user_id', auth()->id())->firstOrFail();
        $pet->pet_name = $request->pet_name;
        $pet->save();
        return redirect()->back()->with('success', 'Nama panggilan Pet berhasil diubah!');
    }

    public function updateUserName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $user = auth()->user();
        $user->name = $request->name;
        $user->save();
        return redirect()->back()->with('success', 'Nama profil berhasil diubah!');
    }

    // Helper: Seeder Otomatis untuk Master Achievement
    private function seedAchievements()
    {
        $achievements = [
            ['code' => 'FIRST_SAVER', 'title' => 'First Saver', 'description' => 'Menabung pertama kali', 'badge_icon' => '🌱'],
            ['code' => 'SAVER_100K', 'title' => 'Century Saver', 'description' => 'Mencapai total tabungan Rp 100.000', 'badge_icon' => '💰'],
            ['code' => 'LEVEL_5', 'title' => 'Elite Saver', 'description' => 'Mencapai Level 5', 'badge_icon' => '👑'],
            ['code' => 'MAX_STAGE', 'title' => 'Godzilla Master', 'description' => 'Mencapai Stage Evolusi Maksimal (Level 10)', 'badge_icon' => '👾'],
        ];

        foreach ($achievements as $ach) {
            Achievement::firstOrCreate(['code' => $ach['code']], $ach);
        }
    }

    // Helper: Logika Membuka Achievement
    private function checkAchievements($user)
    {
        $savingCount = Saving::where('user_id', $user->id)->count();

        // 1. First Saver
        if ($savingCount >= 1) {
            $this->unlock($user, 'FIRST_SAVER');
        }

        // 2. Tabungan 100k
        if ($user->total_savings >= 100000) {
            $this->unlock($user, 'SAVER_100K');
        }

        // 3. Level 5
        if ($user->level >= 5) {
            $this->unlock($user, 'LEVEL_5');
        }

        // 4. Max Level 10
        if ($user->level >= 10) {
            $this->unlock($user, 'MAX_STAGE');
        }
    }

    private function unlock($user, $code)
    {
        $achievement = Achievement::where('code', $code)->first();
        if ($achievement && !$user->achievements->contains($achievement->id)) {
            $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
        }
    }
}