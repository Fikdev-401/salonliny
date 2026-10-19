<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasi;
use App\Models\Layanan;
use App\Models\LandingGallery;
use App\Models\LandingVideo;
use App\Models\LandingTestimoni;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $konfigurasi = Konfigurasi::getInstance();

        $layanans = Layanan::where('status', 'aktif')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get()
            ->groupBy('kategori');

        $galleries = LandingGallery::active()->orderBy('urutan')->orderBy('id')->get();
        $videos = LandingVideo::active()->orderBy('urutan')->orderBy('id')->get();
        $testimonis = LandingTestimoni::active()->orderBy('urutan')->orderBy('id')->get();

        // Build WhatsApp URLs
        $waPhone = $konfigurasi->whatsapp_number ?: $konfigurasi->no_telepon;

        $defaultCtaMessage = $konfigurasi->hero_cta_message
            ?: "Halo " . ($konfigurasi->nama_perusahaan ?: 'LINY Salon') . ", saya ingin melakukan reservasi. Mohon info jadwal kosongnya. Terima kasih!";

        $reservasiUrl = build_landing_wa_url($waPhone, $defaultCtaMessage);

        return view('landing', compact(
            'konfigurasi',
            'layanans',
            'galleries',
            'videos',
            'testimonis',
            'waPhone',
            'reservasiUrl'
        ));
    }

    public function serviceWaUrl(string $nama, ?float $harga = null): ?string
    {
        $konfigurasi = Konfigurasi::getInstance();
        $waPhone = $konfigurasi->whatsapp_number ?: $konfigurasi->no_telepon;

        $message = "Halo, saya tertarik dengan layanan {$nama}";
        if ($harga) {
            $message .= " (estimasi " . rupiah($harga) . ")";
        }
        $message .= ". Mohon info lebih lanjut. Terima kasih!";

        return build_landing_wa_url($waPhone, $message);
    }
}
