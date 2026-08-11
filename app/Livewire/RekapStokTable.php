<?php

namespace App\Livewire;

use Livewire\Component;

class RekapStokTable extends Component
{
    public string $tab = 'masuk';

    public function switchTab(string $tab): void
    {
        $this->tab = in_array($tab, ['masuk', 'keluar']) ? $tab : 'masuk';
    }

    public function render()
    {
        return view('livewire.rekap-stok-table');
    }
}
