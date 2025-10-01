<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Projects dengan project_type yang lebih variatif dan realistis
        $projects = [
            // TKDN Jasa Projects (Form 3.1 - 3.5)
            [
                'name' => 'Gedung Perkantoran 5 Lantai',
                'description' => 'Konstruksi gedung perkantoran modern di Jakarta Pusat',
                'company' => 'PT Pembangunan Jaya',
                'project_type' => 'tkdn_jasa',
                'location' => 'Jakarta Pusat',
            ],
            [
                'name' => 'Rumah Sakit Umum',
                'description' => 'Pembangunan rumah sakit 200 tempat tidur di Bandung',
                'company' => 'PT Kesehatan Indonesia',
                'project_type' => 'tkdn_jasa',
                'location' => 'Bandung',
            ],
            [
                'name' => 'Mall Shopping Center',
                'description' => 'Pusat perbelanjaan 3 lantai dengan area parkir',
                'company' => 'PT Retail Development',
                'project_type' => 'tkdn_jasa',
                'location' => 'Surabaya',
            ],
            [
                'name' => 'Apartemen Premium',
                'description' => 'Tower apartemen 20 lantai dengan fasilitas lengkap',
                'company' => 'PT Property Sejahtera',
                'project_type' => 'tkdn_jasa',
                'location' => 'Jakarta Selatan',
            ],
            [
                'name' => 'Jembatan Layang',
                'description' => 'Konstruksi jembatan layang 2 km di Surabaya',
                'company' => 'PT Infrastruktur Maju',
                'project_type' => 'tkdn_jasa',
                'location' => 'Surabaya',
            ],
            [
                'name' => 'Bandara Internasional',
                'description' => 'Terminal bandara baru dengan kapasitas 10 juta penumpang',
                'company' => 'PT Aviation Indonesia',
                'project_type' => 'tkdn_jasa',
                'location' => 'Bogor',
            ],
            [
                'name' => 'Hotel Bintang 5',
                'description' => 'Hotel mewah dengan 300 kamar dan convention center',
                'company' => 'PT Hospitality Group',
                'project_type' => 'tkdn_jasa',
                'location' => 'Bali',
            ],
            [
                'name' => 'Sekolah Menengah Atas',
                'description' => 'Gedung sekolah 3 lantai dengan laboratorium',
                'company' => 'PT Pendidikan Bangsa',
                'project_type' => 'tkdn_jasa',
                'location' => 'Yogyakarta',
            ],
            [
                'name' => 'Gedung Parkir Bertingkat',
                'description' => 'Parking building 8 lantai dengan 1000 slot',
                'company' => 'PT Parking Solution',
                'project_type' => 'tkdn_jasa',
                'location' => 'Medan',
            ],
            [
                'name' => 'Masjid Agung',
                'description' => 'Masjid dengan kapasitas 5000 jamaah',
                'company' => 'PT Wakaf Indonesia',
                'project_type' => 'tkdn_jasa',
                'location' => 'Aceh',
            ],
            [
                'name' => 'Stasiun Kereta Api',
                'description' => 'Stasiun modern dengan 4 peron',
                'company' => 'PT Kereta Api Indonesia',
                'project_type' => 'tkdn_jasa',
                'location' => 'Semarang',
            ],
            [
                'name' => 'Gedung Pemerintahan',
                'description' => 'Kantor pemerintah daerah dengan 10 lantai',
                'company' => 'PT Pembangunan Negara',
                'project_type' => 'tkdn_jasa',
                'location' => 'Palembang',
            ],
            [
                'name' => 'Universitas Kampus',
                'description' => 'Gedung kuliah dengan 15 ruang kelas',
                'company' => 'PT Pendidikan Tinggi',
                'project_type' => 'tkdn_jasa',
                'location' => 'Malang',
            ],
            [
                'name' => 'Pusat Olahraga',
                'description' => 'GOR dengan kapasitas 5000 penonton',
                'company' => 'PT Olahraga Nasional',
                'project_type' => 'tkdn_jasa',
                'location' => 'Makassar',
            ],

            // TKDN Barang & Jasa Projects (Form 4.1 - 4.7)
            [
                'name' => 'Pabrik Manufaktur Elektronik',
                'description' => 'Pabrik dengan area produksi 5000 m2 untuk komponen elektronik',
                'company' => 'PT Industri Maju',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Batam',
            ],
            [
                'name' => 'Pabrik Tekstil Modern',
                'description' => 'Pabrik tekstil dengan mesin otomatis dan sistem pengolahan limbah',
                'company' => 'PT Tekstil Indonesia',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Cirebon',
            ],
            [
                'name' => 'Pabrik Makanan & Minuman',
                'description' => 'Pabrik pengolahan makanan kemasan dengan standar halal',
                'company' => 'PT Pangan Sejahtera',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Bekasi',
            ],
            [
                'name' => 'Pabrik Kimia & Farmasi',
                'description' => 'Pabrik bahan kimia dan obat-obatan dengan laboratorium R&D',
                'company' => 'PT Kimia Farma',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Tangerang',
            ],
            [
                'name' => 'Pabrik Otomotif',
                'description' => 'Pabrik perakitan kendaraan dengan sistem produksi just-in-time',
                'company' => 'PT Mobil Indonesia',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Karawang',
            ],
            [
                'name' => 'Pabrik Plastik & Kemasan',
                'description' => 'Pabrik produksi plastik dan kemasan ramah lingkungan',
                'company' => 'PT Plastik Hijau',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Sidoarjo',
            ],
            [
                'name' => 'Pabrik Kertas & Pulp',
                'description' => 'Pabrik kertas dengan sistem daur ulang dan pengolahan limbah',
                'company' => 'PT Kertas Nusantara',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Riau',
            ],
            [
                'name' => 'Pabrik Semen & Beton',
                'description' => 'Pabrik semen dengan teknologi ramah lingkungan',
                'company' => 'PT Semen Indonesia',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Gresik',
            ],
            [
                'name' => 'Pabrik Logam & Baja',
                'description' => 'Pabrik pengolahan logam dan baja dengan teknologi modern',
                'company' => 'PT Baja Indonesia',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Cilegon',
            ],
            [
                'name' => 'Pabrik Keramik & Ubin',
                'description' => 'Pabrik keramik dan ubin dengan desain modern',
                'company' => 'PT Keramik Nusantara',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Lampung',
            ],
            [
                'name' => 'Pabrik Furniture & Kayu',
                'description' => 'Pabrik furniture dan pengolahan kayu dengan sertifikasi FSC',
                'company' => 'PT Furniture Indonesia',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Kudus',
            ],
            [
                'name' => 'Pabrik Alat Berat',
                'description' => 'Pabrik produksi alat berat dan mesin konstruksi',
                'company' => 'PT Alat Berat Indonesia',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Cikarang',
            ],
            [
                'name' => 'Pabrik Komponen Elektronik',
                'description' => 'Pabrik komponen elektronik dan perangkat IoT',
                'company' => 'PT Elektronik Nusantara',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Bandung',
            ],
            [
                'name' => 'Pabrik Teknologi Hijau',
                'description' => 'Pabrik teknologi ramah lingkungan dan energi terbarukan',
                'company' => 'PT Teknologi Hijau',
                'project_type' => 'tkdn_barang_jasa',
                'location' => 'Solo',
            ],
        ];

        $statusList = ['draft', 'on_progress', 'completed'];


        foreach ($projects as $project) {
            $start = now()->subDays(rand(30, 730)); // random 30–730 hari ke belakang
            $end = (clone $start)->addDays(rand(30, 365));

            Project::create([
                'name' => $project['name'],
                'description' => $project['description'],
                'company' => $project['company'],
                'project_type' => $project['project_type'],
                'status' => $statusList[array_rand($statusList)], // ambil random dari array
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'location' => $project['location'],
            ]);
        }
    }
}
