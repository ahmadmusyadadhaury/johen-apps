<?php

namespace App\Support;

use Illuminate\Http\Request;

class DivisionMenu
{
    private const ICONS = [
        'ticket' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15v5.25a2.25 2.25 0 010 4.5v2.25h-15v-2.25a2.25 2.25 0 010-4.5V7.5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v1.5m0 2.25v1.5"/>',
        'project' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
        'report' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>',
        'tracking' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
        'activity' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>',
        'influencer' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
        'content' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>',
        'presensi' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'cuti' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'users' => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
        'manual' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
        'default' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>',
    ];

    private const MENUS = [
        'IT' => [
            'label' => 'Divisi IT',
            'items' => [
                ['label' => 'Ticketing IT', 'route' => 'it.tickets.index', 'icon' => 'ticket', 'desc' => 'Kelola tiket bantuan IT'],
                ['label' => 'Project IT', 'route' => 'it.project', 'icon' => 'project', 'desc' => 'Manajemen project IT'],
                ['label' => 'Jadwal Maintenance', 'route' => 'it.maintenance', 'icon' => 'calendar', 'desc' => 'Jadwal maintenance PC & server'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
            ],
        ],
        'Admin Transaksi' => [
            'label' => 'Divisi Admin',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'hris.daily-tracking-admin', 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Johen PUBG' => [
            'label' => 'Divisi Johen PUBG',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'PUBG'], 'query' => ['divisi' => 'PUBG'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Monkey PUBG' => [
            'label' => 'Divisi Monkey PUBG',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'Monkey PUBG'], 'query' => ['divisi' => 'Monkey PUBG'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Free Fire' => [
            'label' => 'Divisi Free Fire',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'Free Fire'], 'query' => ['divisi' => 'Free Fire'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Roblox' => [
            'label' => 'Divisi Roblox',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'Roblox'], 'query' => ['divisi' => 'Roblox'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Valorant' => [
            'label' => 'Divisi Valorant',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'Valorant'], 'query' => ['divisi' => 'Valorant'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'E-Football' => [
            'label' => 'Divisi E-football',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'E-football'], 'query' => ['divisi' => 'E-football'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Mobile Legend' => [
            'label' => 'Divisi MLBB',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'MLBB'], 'query' => ['divisi' => 'MLBB'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'FC Mobile' => [
            'label' => 'Divisi FC Mobile',
            'items' => [
                ['label' => 'Daily Tracking', 'route' => 'pubg.daily-tracking', 'params' => ['divisi' => 'FC Mobile'], 'query' => ['divisi' => 'FC Mobile'], 'icon' => 'tracking', 'desc' => 'Pantau aktivitas harian divisi'],
                ['label' => 'Weekly Plan Report', 'route' => 'hris.weekly-report', 'icon' => 'report', 'desc' => 'Laporan rencana kerja mingguan'],
                ['label' => 'Activity Competitor', 'route' => 'hris.activity-competitor', 'icon' => 'activity', 'desc' => 'Pantau aktivitas kompetitor'],
            ],
        ],
        'Creative' => [
            'label' => 'Divisi Creative',
            'items' => [
                ['label' => 'Influencer', 'route' => 'hris.influencer', 'icon' => 'influencer', 'desc' => 'Kelola data influencer'],
                ['label' => 'Kalender Event', 'route' => 'hris.kalender-event', 'icon' => 'calendar', 'desc' => 'Jadwal event dan kegiatan'],
                ['label' => 'Content Plan', 'route' => 'hris.content-plan', 'icon' => 'content', 'desc' => 'Rencana konten divisi'],
            ],
        ],
        'HRGA' => [
            'label' => 'Divisi HRGA',
            'items' => [
                ['label' => 'Presensi', 'route' => 'hris.absensi', 'icon' => 'presensi', 'desc' => 'Absensi dan kehadiran'],
                ['label' => 'Cuti & Izin', 'route' => 'hris.cuti-izin', 'icon' => 'cuti', 'desc' => 'Pengajuan cuti dan izin'],
                ['label' => 'Kontrak Kerja', 'route' => 'hris.kontrak-kerja', 'icon' => 'report', 'desc' => 'Data kontrak kerja karyawan'],
                ['label' => 'Struktur Organisasi', 'route' => 'hris.struktur-organisasi', 'icon' => 'users', 'desc' => 'Bagan organisasi perusahaan'],
                ['label' => 'Manual Book', 'route' => 'hris.manual-book', 'icon' => 'manual', 'desc' => 'Panduan penggunaan aplikasi'],
            ],
        ],
    ];

    private const DEFAULT_MENU = [
        'label' => 'Menu Umum',
        'items' => [
            ['label' => 'Presensi', 'route' => 'hris.absensi', 'icon' => 'presensi', 'desc' => 'Absensi dan kehadiran'],
            ['label' => 'Cuti & Izin', 'route' => 'hris.cuti-izin', 'icon' => 'cuti', 'desc' => 'Pengajuan cuti dan izin'],
            ['label' => 'Kontrak Kerja', 'route' => 'hris.kontrak-kerja', 'icon' => 'report', 'desc' => 'Data kontrak kerja karyawan'],
            ['label' => 'Jobdesk', 'route' => 'hris.jobdesk', 'icon' => 'content', 'desc' => 'Jobdesk dan tanggung jawab'],
            ['label' => 'Struktur Organisasi', 'route' => 'hris.struktur-organisasi', 'icon' => 'users', 'desc' => 'Bagan organisasi perusahaan'],
            ['label' => 'Bantuan IT', 'route' => 'it.tickets.index', 'icon' => 'ticket', 'desc' => 'Kelola tiket bantuan IT'],
        ],
    ];

    public static function for(string $nama): array
    {
        $normalized = preg_replace('/^\d+\s+/', '', trim($nama));
        $menu = self::MENUS[$normalized] ?? self::DEFAULT_MENU;

        return [
            'label' => $menu['label'],
            'items' => array_map(fn ($item) => [
                'label' => $item['label'],
                'route' => $item['route'],
                'params' => $item['params'] ?? [],
                'query' => $item['query'] ?? [],
                'icon' => $item['icon'],
                'desc' => $item['desc'] ?? '',
            ], $menu['items']),
        ];
    }

    public static function icon(string $key): string
    {
        return self::ICONS[$key] ?? self::ICONS['default'];
    }

    public static function isActive(array $item, Request $request): bool
    {
        if (!$request->routeIs($item['route'])) {
            return false;
        }

        foreach ($item['query'] as $key => $value) {
            if ($request->query($key) !== $value) {
                return false;
            }
        }

        return true;
    }

    public static function matchesAny(array $menu, Request $request): bool
    {
        foreach ($menu['items'] as $item) {
            if (self::isActive($item, $request)) {
                return true;
            }
        }

        return false;
    }
}
