<?php

namespace App\Livewire;

use App\Models\ContentPlan;
use App\Models\ContentPlanReport;
use Livewire\Component;
use Livewire\WithPagination;

class ContentPlanTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;
    public string $activeTab = 'Desain Grafis';

    public string $posisi = '';
    public ?string $judul = '';
    public string $deskripsi = '';
    public string $pic = '';
    public string $jenis_desain = '';
    public string $link = '';
    public string $deadline = '';
    public string $status = 'concept';

    public string $create = '';
    public string $take = '';
    public string $vo = '';
    public string $post = '';
    public string $days = '';
    public string $waktu = '';
    public string $akun = '';
    public string $divisi = '';
    public string $pic_utama = '';
    public string $topic = '';
    public string $script = '';
    public string $creator = '';
    public string $goals_content = '';
    public string $content_pillar = '';
    public string $type_of_content = '';
    public string $reference_content = '';
    public string $storyline = '';
    public string $caption = '';
    public string $revisi = '';
    public bool $acc_to_posting = false;

    public bool $showReportModal = false;
    public bool $showReportForm = false;
    public ?int $reportContentPlanId = null;
    public ?int $reportEditId = null;
    public int $reportWeek = 1;
    public int $reportViews = 0;
    public int $reportLikes = 0;
    public int $reportComment = 0;
    public int $reportSave = 0;
    public int $reportShare = 0;
    public int $reportFollowers = 0;

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user->isKoordinatorCreative()) {
            $pos = $user->employee?->mainPosition()?->nama;
            if (in_array($pos, ['Desain Grafis', 'Content Creator', 'Video Animator'])) {
                $this->activeTab = $pos;
            }
        }
        $this->posisi = $this->activeTab;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->posisi = $tab;
        $this->resetPage();
    }

    public function openNew(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->resetInput();
        $this->posisi = $this->activeTab;
        $this->status = in_array($this->activeTab, ['Content Creator', 'Desain Grafis', 'Video Animator']) ? 'concept' : 'plan';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $item = ContentPlan::findOrFail($id);
        $this->editId = $item->id;
        $this->posisi = $item->posisi;
        $this->judul = $item->judul ?? '';
        $this->create = $item->create ?? '';
        $this->take = $item->take ?? '';
        $this->post = $item->post ?? '';
        $this->days = $item->days ?? '';
        $this->waktu = $item->waktu ?? '';
        $this->akun = $item->akun ?? '';
        $this->divisi = $item->divisi ?? '';
        $this->deskripsi = $item->deskripsi ?? '';
        $this->pic = $item->pic ?? '';
        $this->pic_utama = $item->pic_utama ?? '';
        $this->jenis_desain = $item->jenis_desain ?? '';
        $this->topic = $item->topic ?? '';
        $this->vo = $item->vo ?? '';
        $this->script = $item->script ?? '';
        $this->creator = $item->creator ?? '';
        $this->goals_content = $item->goals_content ?? '';
        $this->content_pillar = $item->content_pillar ?? '';
        $this->type_of_content = $item->type_of_content ?? '';
        $this->reference_content = $item->reference_content ?? '';
        $this->storyline = $item->storyline ?? '';
        $this->caption = $item->caption ?? '';
        $this->revisi = $item->revisi ?? '';
        $this->acc_to_posting = $item->acc_to_posting ?? false;
        $this->link = $item->link ?? '';
        $this->deadline = $item->deadline?->format('Y-m-d') ?? '';
        $this->status = $item->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $rules = [];

        if ($this->posisi === 'Content Creator') {
            $rules = [
                'take' => 'nullable|string|max:255',
                'post' => 'nullable|string|max:255',
                'days' => 'nullable|string|max:255',
                'waktu' => 'nullable|string|max:255',
                'akun' => 'nullable|string|max:255',
                'status' => 'required|in:concept,scripting,editing,revisi,post,hold',
                'pic_utama' => 'nullable|string|max:255',
                'topic' => 'nullable|string|max:255',
                'creator' => 'nullable|string|max:255',
                'goals_content' => 'nullable|string|max:255',
                'content_pillar' => 'nullable|string|max:255',
                'type_of_content' => 'nullable|string|max:255',
                'reference_content' => 'nullable|string',
                'storyline' => 'nullable|string',
                'caption' => 'nullable|string',
                'revisi' => 'nullable|string|max:255',
                'acc_to_posting' => 'boolean',
            ];
        } else {
            $rules = [
                'judul' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'pic' => 'nullable|string|max:255',
                'link' => 'nullable|string|max:255',
                'deadline' => 'nullable|date',
                'status' => 'required|in:plan,proses,selesai',
            ];

            if ($this->posisi === 'Desain Grafis') {
                $rules['jenis_desain'] = 'nullable|string|max:255';
                $rules['divisi'] = 'nullable|string|max:255';
                $rules['create'] = 'nullable|string|max:255';
                $rules['post'] = 'nullable|string|max:255';
                $rules['days'] = 'nullable|string|max:255';
                $rules['waktu'] = 'nullable|string|max:255';
                $rules['akun'] = 'nullable|string|max:255';
                $rules['topic'] = 'nullable|string|max:255';
                $rules['type_of_content'] = 'nullable|string|max:255';
                $rules['caption'] = 'nullable|string';
                $rules['status'] = 'required|in:concept,scripting,editing,revisi,post,hold';
            }

            if ($this->posisi === 'Video Animator') {
                $rules['take'] = 'nullable|string|max:255';
                $rules['vo'] = 'nullable|string|max:255';
                $rules['post'] = 'nullable|string|max:255';
                $rules['days'] = 'nullable|string|max:255';
                $rules['waktu'] = 'nullable|string|max:255';
                $rules['akun'] = 'nullable|string|max:255';
                $rules['pic'] = 'nullable|string|max:255';
                $rules['topic'] = 'nullable|string|max:255';
                $rules['script'] = 'nullable|string';
                $rules['caption'] = 'nullable|string';
                $rules['revisi'] = 'nullable|string|max:255';
                $rules['acc_to_posting'] = 'boolean';
                $rules['status'] = 'required|in:concept,scripting,editing,revisi,ready,post';
                $rules['judul'] = 'nullable|string|max:255';
            }
        }

        $this->validate($rules);

        $data = [
            'posisi' => $this->posisi,
        ];

        if ($this->posisi === 'Content Creator') {
            $data += [
                'judul' => $this->judul ?: null,
                'deskripsi' => $this->deskripsi ?: null,
                'take' => $this->take ?: null,
                'post' => $this->post ?: null,
                'days' => $this->days ?: null,
                'waktu' => $this->waktu ?: null,
                'akun' => $this->akun ?: null,
                'pic' => $this->pic ?: null,
                'pic_utama' => $this->pic_utama ?: null,
                'topic' => $this->topic ?: null,
                'creator' => $this->creator ?: null,
                'goals_content' => $this->goals_content ?: null,
                'content_pillar' => $this->content_pillar ?: null,
                'type_of_content' => $this->type_of_content ?: null,
                'reference_content' => $this->reference_content ?: null,
                'storyline' => $this->storyline ?: null,
                'caption' => $this->caption ?: null,
                'revisi' => $this->revisi ?: null,
                'acc_to_posting' => $this->acc_to_posting,
                'link' => $this->link ?: null,
                'deadline' => $this->deadline ?: null,
                'status' => $this->status,
            ];
        } else {
            $data += [
                'judul' => $this->judul ?: null,
                'deskripsi' => $this->deskripsi ?: null,
                'pic' => $this->pic ?: null,
                'link' => $this->link ?: null,
                'deadline' => $this->deadline ?: null,
                'status' => $this->status,
            ];

            if ($this->posisi === 'Desain Grafis') {
                $data['jenis_desain'] = $this->jenis_desain ?: null;
                $data['create'] = $this->create ?: null;
                $data['post'] = $this->post ?: null;
                $data['days'] = $this->days ?: null;
                $data['waktu'] = $this->waktu ?: null;
                $data['akun'] = $this->akun ?: null;
                $data['divisi'] = $this->divisi ?: null;
                $data['topic'] = $this->topic ?: null;
                $data['type_of_content'] = $this->type_of_content ?: null;
                $data['caption'] = $this->caption ?: null;
            }

            if ($this->posisi === 'Video Animator') {
                $data['take'] = $this->take ?: null;
                $data['vo'] = $this->vo ?: null;
                $data['post'] = $this->post ?: null;
                $data['days'] = $this->days ?: null;
                $data['waktu'] = $this->waktu ?: null;
                $data['akun'] = $this->akun ?: null;
                $data['pic'] = $this->pic ?: null;
                $data['topic'] = $this->topic ?: null;
                $data['script'] = $this->script ?: null;
                $data['caption'] = $this->caption ?: null;
                $data['revisi'] = $this->revisi ?: null;
                $data['acc_to_posting'] = $this->acc_to_posting;
            }
        }

        if ($this->editId) {
            ContentPlan::where('id', $this->editId)->update($data);
        } else {
            ContentPlan::create($data);
        }

        $this->close();
        $this->dispatch('notify', type: 'success', message: $this->editId ? 'Content plan diperbarui.' : 'Content plan ditambahkan.');
    }

    public function delete(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        ContentPlan::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Content plan dihapus.');
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->editId = null;
        $this->resetInput();
    }

    private function resetInput(): void
    {
        $this->judul = '';
        $this->deskripsi = '';
        $this->pic = '';
        $this->jenis_desain = '';
        $this->link = '';
        $this->deadline = '';
        $this->status = 'concept';
        $this->create = '';
        $this->take = '';
        $this->post = '';
        $this->days = '';
        $this->waktu = '';
        $this->akun = '';
        $this->divisi = '';
        $this->pic_utama = '';
        $this->topic = '';
        $this->creator = '';
        $this->goals_content = '';
        $this->content_pillar = '';
        $this->type_of_content = '';
        $this->reference_content = '';
        $this->storyline = '';
        $this->caption = '';
        $this->vo = '';
        $this->script = '';
        $this->revisi = '';
        $this->acc_to_posting = false;
    }

    public function openReport(int $id): void
    {
        $this->reportContentPlanId = $id;
        $this->reportEditId = null;
        $this->reportWeek = 1;
        $this->reportViews = 0;
        $this->reportLikes = 0;
        $this->reportComment = 0;
        $this->reportSave = 0;
        $this->reportShare = 0;
        $this->reportFollowers = 0;
        $this->showReportForm = false;
        $this->showReportModal = true;
    }

    public function openEditReport(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $report = ContentPlanReport::findOrFail($id);
        $this->reportContentPlanId = $report->content_plan_id;
        $this->reportEditId = $report->id;
        $this->reportWeek = $report->week;
        $this->reportViews = $report->views;
        $this->reportLikes = $report->likes;
        $this->reportComment = $report->comment;
        $this->reportSave = $report->save;
        $this->reportShare = $report->share;
        $this->reportFollowers = $report->followers;
        $this->showReportForm = true;
        $this->showReportModal = true;
    }

    public function saveReport(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate([
            'reportWeek' => 'required|integer|min:1|max:52',
            'reportViews' => 'required|integer|min:0',
            'reportLikes' => 'required|integer|min:0',
            'reportComment' => 'required|integer|min:0',
            'reportSave' => 'required|integer|min:0',
            'reportShare' => 'required|integer|min:0',
            'reportFollowers' => 'required|integer|min:0',
        ]);

        $data = [
            'content_plan_id' => $this->reportContentPlanId,
            'week' => $this->reportWeek,
            'views' => $this->reportViews,
            'likes' => $this->reportLikes,
            'comment' => $this->reportComment,
            'save' => $this->reportSave,
            'share' => $this->reportShare,
            'followers' => $this->reportFollowers,
        ];

        if ($this->reportEditId) {
            ContentPlanReport::where('id', $this->reportEditId)->update($data);
        } else {
            ContentPlanReport::create($data);
        }

        $this->reportEditId = null;
        $this->showReportForm = false;
        $this->dispatch('notify', type: 'success', message: 'Report berhasil disimpan.');
    }

    public function deleteReport(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        ContentPlanReport::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Report dihapus.');
    }

    public function openNewReport(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $maxWeek = ContentPlanReport::where('content_plan_id', $this->reportContentPlanId)->max('week') ?: 0;
        $this->reportEditId = null;
        $this->reportWeek = $maxWeek + 1;
        $this->reportViews = 0;
        $this->reportLikes = 0;
        $this->reportComment = 0;
        $this->reportSave = 0;
        $this->reportShare = 0;
        $this->reportFollowers = 0;
        $this->showReportForm = true;
    }

    public function closeReport(): void
    {
        $this->showReportModal = false;
        $this->reportContentPlanId = null;
        $this->reportEditId = null;
    }

    public function render()
    {
        $items = ContentPlan::where('posisi', $this->activeTab)
            ->with('reports')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.content-plan-table', [
            'items' => $items,
        ]);
    }
}
