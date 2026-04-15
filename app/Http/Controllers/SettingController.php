<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    private $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/settings_home.json');
    }

    private function getSettings()
    {
        if (File::exists($this->filePath)) {
            return json_decode(File::get($this->filePath), true);
        }

        return [
            'hero_pill' => 'Platform Pengaduan Digital SMKN 1 Bondowoso',
            'hero_title_1' => 'Suarakan',
            'hero_title_gradient' => 'Aspirasimu,',
            'hero_title_2' => 'Wujudkan Perubahan!',
            'hero_description' => 'Platform digital transparan untuk menyampaikan aspirasi dan melaporkan masalah di sekolah. Setiap suara didengar, setiap laporan ditindaklanjuti.',
            'feature_title' => 'Mengapa Harus Melaporkan?',
            'feature_description' => 'Dirancang untuk memberikan pengalaman pelaporan yang mudah, aman, dan efektif',
            // Default Features
            'features' => [
                ['title' => 'Aman & Terpercaya', 'desc' => 'Identitas Anda kami rahasiakan dan sistem dilindungi enkripsi terkini.'],
                ['title' => 'Respon Cepat', 'desc' => 'Laporan yang masuk akan ditinjau dan diteruskan dalam waktu 2x24 jam.'],
                ['title' => 'Transparan', 'desc' => 'Lacak progress laporan Anda secara real-time dari riwayat status.']
            ]
        ];
    }

    public function editHome()
    {
        // Pastikan hanya admin (Bisa memakai middleware, namun dipastikan dari construct/routes)
        if (!auth()->user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        $settings = $this->getSettings();
        return view('settings.home', compact('settings'));
    }

    public function updateHome(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'hero_pill' => 'required|string|max:100',
            'hero_title_1' => 'required|string|max:100',
            'hero_title_gradient' => 'required|string|max:100',
            'hero_title_2' => 'required|string|max:100',
            'hero_description' => 'required|string|max:500',
            'feature_title' => 'required|string|max:100',
            'feature_description' => 'required|string|max:500',
        ]);

        $settings = $this->getSettings();
        $settings['hero_pill'] = $request->hero_pill;
        $settings['hero_title_1'] = $request->hero_title_1;
        $settings['hero_title_gradient'] = $request->hero_title_gradient;
        $settings['hero_title_2'] = $request->hero_title_2;
        $settings['hero_description'] = $request->hero_description;
        $settings['feature_title'] = $request->feature_title;
        $settings['feature_description'] = $request->feature_description;

        File::put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->back()->with('success', 'Konten Halaman Beranda berhasil diperbarui!');
    }
}
