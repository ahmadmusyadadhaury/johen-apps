<?php

namespace App\Support;

class ContractEvaluationConfig
{
    public const SCALE_MIN = 0;

    public const SCALE_MAX = 4;

    public const PASSING_THRESHOLD = 2.01;

    public static function categories(): array
    {
        return [
            [
                'key' => 'disiplin',
                'label' => 'Disiplin',
                'code' => 'A',
                'weight' => 30,
                'section' => 'disiplin',
                'indicators' => [
                    [
                        'field' => 'i_kehadiran',
                        'label' => 'Kehadiran Karyawan',
                        'weight' => 10,
                        'desc' => 'Rata-rata ketidakhadiran karyawan selama masa kontrak.',
                        'scale' => ['>15 kali', '13-15 kali', '9-12 kali', '5-8 kali', '0-4 kali'],
                    ],
                    [
                        'field' => 'i_ketepatan_waktu',
                        'label' => 'Ketepatan Waktu',
                        'weight' => 10,
                        'desc' => 'Ketepatan waktu hadir dan menyelesaikan pekerjaan sesuai jadwal.',
                        'scale' => null,
                    ],
                    [
                        'field' => 'i_kepatuhan_peraturan',
                        'label' => 'Kepatuhan terhadap Peraturan Perusahaan',
                        'weight' => 10,
                        'desc' => 'Kepatuhan terhadap tata tertib, SOP, dan arahan perusahaan.',
                        'scale' => null,
                    ],
                ],
            ],
            [
                'key' => 'kinerja',
                'label' => 'Kinerja Kerja',
                'code' => 'B',
                'weight' => 45,
                'section' => 'kinerja',
                'indicators' => [
                    [
                        'field' => 'i_tanggung_jawab',
                        'label' => 'Tanggung Jawab',
                        'weight' => 15,
                        'desc' => 'Sikap bertanggung jawab atas tugas dan amanah yang diberikan.',
                        'scale' => null,
                    ],
                    [
                        'field' => 'i_kualitas_kerja',
                        'label' => 'Kualitas Hasil Kerja',
                        'weight' => 10,
                        'desc' => 'Mutu dan ketelitian hasil kerja yang diselesaikan.',
                        'scale' => null,
                    ],
                    [
                        'field' => 'i_produktivitas',
                        'label' => 'Produktivitas Kerja',
                        'weight' => 10,
                        'desc' => 'Jumlah output kerja dibandingkan dengan target waktu.',
                        'scale' => null,
                    ],
                    [
                        'field' => 'i_penyelesaian_tugas',
                        'label' => 'Kemampuan Menyelesaikan Tugas',
                        'weight' => 10,
                        'desc' => 'Kemampuan menyelesaikan tugas secara mandiri hingga selesai.',
                        'scale' => null,
                    ],
                ],
            ],
            [
                'key' => 'sikap',
                'label' => 'Sikap Kerja Sama',
                'code' => 'C',
                'weight' => 15,
                'section' => 'sikap',
                'indicators' => [
                    [
                        'field' => 'i_komunikasi',
                        'label' => 'Komunikasi dan Etika Kerja',
                        'weight' => 5,
                        'desc' => 'Etika komunikasi terhadap atasan, rekan kerja, dan pelanggan.',
                        'scale' => null,
                    ],
                    [
                        'field' => 'i_kerja_sama_tim',
                        'label' => 'Kerja Sama Tim',
                        'weight' => 5,
                        'desc' => 'Kemampuan bekerja sama dan membantu dalam tim.',
                        'scale' => null,
                    ],
                    [
                        'field' => 'i_inisiatif',
                        'label' => 'Inisiatif dan Loyalitas Kerja',
                        'weight' => 5,
                        'desc' => 'Inisiatif memperbaiki cara kerja serta loyalitas pada perusahaan.',
                        'scale' => null,
                    ],
                ],
            ],
            [
                'key' => 'hasil',
                'label' => 'Hasil Kerja',
                'code' => 'D',
                'weight' => 10,
                'section' => 'hasil',
                'indicators' => [
                    [
                        'field' => 'i_pencapaian_target',
                        'label' => 'Pencapaian Target Kerja',
                        'weight' => 5,
                        'desc' => 'Pencapaian target kerja yang ditetapkan atasan.',
                        'scale' => ['Jauh di bawah target', 'Di bawah target', 'Mencapai target', 'Melebihi target', 'Jauh melebihi target'],
                    ],
                    [
                        'field' => 'i_penghargaan_sanksi',
                        'label' => 'Penghargaan dan Sanksi',
                        'weight' => 5,
                        'desc' => 'Penghargaan yang pernah diraih atau sanksi yang pernah diterima.',
                        'scale' => ['Pernah sanksi berat', 'Pernah SP / sanksi', 'Tanpa penghargaan & sanksi', 'Pernah penghargaan', 'Raihan penghargaan baik'],
                    ],
                ],
            ],
        ];
    }

    public static function indicators(): array
    {
        $flat = [];

        foreach (self::categories() as $category) {
            foreach ($category['indicators'] as $indicator) {
                $indicator['category_key'] = $category['key'];
                $indicator['category_label'] = $category['label'];
                $indicator['section'] = $category['section'];
                $flat[$indicator['field']] = $indicator;
            }
        }

        return $flat;
    }

    public static function totalWeight(): int
    {
        return array_sum(array_map(fn ($c) => $c['weight'], self::categories()));
    }

    public static function indicatorCount(): int
    {
        return count(self::indicators());
    }

    public static function sections(): array
    {
        return [
            ['id' => 'info', 'label' => 'Informasi Karyawan'],
            ['id' => 'disiplin', 'label' => 'Disiplin'],
            ['id' => 'kinerja', 'label' => 'Kinerja Kerja'],
            ['id' => 'sikap', 'label' => 'Sikap Kerja Sama'],
            ['id' => 'hasil', 'label' => 'Hasil Kerja'],
            ['id' => 'catatan', 'label' => 'Catatan Evaluasi'],
            ['id' => 'rekomendasi', 'label' => 'Rekomendasi'],
            ['id' => 'review', 'label' => 'Final Review'],
        ];
    }

    public static function devTags(): array
    {
        return [
            'Coaching',
            'Training',
            'Mentoring',
            'Peningkatan tanggung jawab',
            'Improvement target',
            'Tidak ada rekomendasi',
            'Lainnya',
        ];
    }
}
