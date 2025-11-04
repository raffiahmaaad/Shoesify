<?php

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public array $addresses = [];

    public array $form = [
        'id' => null,
        'label' => '',
        'recipient_name' => '',
        'phone' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'province' => '',
        'postal_code' => '',
        'country' => 'Indonesia',
        'is_default' => false,
    ];

    public bool $showForm = false;
    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    public function mount(): void
    {
        $this->refreshList();
    }

    #[\Livewire\Attributes\On('address-updated')]
    public function refreshList(): void
    {
        $this->addresses = Auth::user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Address $address) => $address->only([
                'id',
                'label',
                'recipient_name',
                'phone',
                'address_line1',
                'address_line2',
                'city',
                'province',
                'postal_code',
                'country',
                'is_default',
            ]))
            ->all();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $addressId): void
    {
        $address = collect($this->addresses)->firstWhere('id', $addressId);
        if (! $address) {
            return;
        }

        $this->form = array_merge($this->form, $address);
        $this->form['is_default'] = (bool) $address['is_default'];
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.label' => ['required', 'string', 'max:60'],
            'form.recipient_name' => ['required', 'string', 'max:120'],
            'form.phone' => ['required', 'string', 'max:30'],
            'form.address_line1' => ['required', 'string', 'max:255'],
            'form.address_line2' => ['nullable', 'string', 'max:255'],
            'form.city' => ['required', 'string', 'max:120'],
            'form.province' => ['required', 'string', 'max:120'],
            'form.postal_code' => ['required', 'string', 'max:20'],
            'form.country' => ['required', 'string', 'max:80'],
            'form.is_default' => ['boolean'],
        ])['form'];

        $user = Auth::user();

        $address = $user->addresses()->updateOrCreate(
            ['id' => $validated['id']],
            collect($validated)->except('id')->toArray()
        );

        if ($validated['is_default']) {
            $user->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
            $address->is_default = true;
            $address->save();
        }

        $this->showForm = false;
        $this->resetForm();
        $this->refreshList();
    }

    public function confirmDelete(int $addressId): void
    {
        $this->deleteId = $addressId;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            Auth::user()->addresses()->where('id', $this->deleteId)->delete();
        }

        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->refreshList();
    }

    public function markDefault(int $addressId): void
    {
        $user = Auth::user();

        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $addressId)->update(['is_default' => true]);

        $this->refreshList();
    }

    private function resetForm(): void
    {
        $this->form = [
            'id' => null,
            'label' => '',
            'recipient_name' => '',
            'phone' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city' => '',
            'province' => '',
            'postal_code' => '',
            'country' => 'Indonesia',
            'is_default' => false,
        ];
    }
}; ?>

<x-account.layout title="Buku Alamat" subtitle="Kelola alamat default, pengiriman kantor, dan alamat keluarga untuk checkout yang lebih cepat.">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-white/60">Menambahkan lebih dari satu alamat memudahkan kamu memilih pengiriman saat checkout.</p>
        <button type="button" class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-5 py-2 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5" wire:click="create">
            Tambah alamat
        </button>
    </div>

    @if ($showForm)
        <div class="glass-panel space-y-4 p-6">
            <header class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-white">{{ $form['id'] ? 'Edit alamat' : 'Alamat baru' }}</h3>
                    <p class="text-xs text-white/50">Isi detail alamat secara lengkap agar kurir mudah menemukan lokasi.</p>
                </div>
                <button type="button" class="text-xs text-white/50 transition hover:text-white" wire:click="$set('showForm', false)">Tutup</button>
            </header>
            <form wire:submit.prevent="save" class="grid gap-4 md:grid-cols-2">
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Label</span>
                    <input type="text" wire:model.defer="form.label" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.label') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Nama penerima</span>
                    <input type="text" wire:model.defer="form.recipient_name" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.recipient_name') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">No. telepon</span>
                    <input type="text" wire:model.defer="form.phone" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.phone') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Kota</span>
                    <input type="text" wire:model.defer="form.city" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.city') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Provinsi</span>
                    <input type="text" wire:model.defer="form.province" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.province') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Kode pos</span>
                    <input type="text" wire:model.defer="form.postal_code" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.postal_code') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Negara</span>
                    <input type="text" wire:model.defer="form.country" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('form.country') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2 md:col-span-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Alamat lengkap</span>
                    <textarea wire:model.defer="form.address_line1" rows="2" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30"></textarea>
                    @error('form.address_line1') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2 md:col-span-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Detail tambahan</span>
                    <textarea wire:model.defer="form.address_line2" rows="2" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30"></textarea>
                </label>
                <label class="flex items-center gap-2 text-xs text-white/60 md:col-span-2">
                    <input type="checkbox" wire:model.defer="form.is_default" class="h-4 w-4 rounded border-white/20 bg-transparent text-[#4de4d4] focus:ring-[#4de4d4]/40">
                    Jadikan sebagai alamat utama
                </label>
                <div class="md:col-span-2 flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-5 py-2 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5">
                        Simpan alamat
                    </button>
                    <button type="button" class="text-xs text-white/50 transition hover:text-white" wire:click="$set('showForm', false)">Batalkan</button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($addresses as $address)
            <article class="glass-panel space-y-3 p-6" wire:key="address-card-{{ $address['id'] }}">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $address['label'] }}</p>
                        <p class="text-xs text-white/50">{{ $address['recipient_name'] }} • {{ $address['phone'] }}</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        @if ($address['is_default'])
                            <span class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4]/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-[#4de4d4]">
                                Default
                            </span>
                        @else
                            <button type="button" class="text-white/50 transition hover:text-white" wire:click="markDefault({{ $address['id'] }})">Jadikan default</button>
                        @endif
                    </div>
                </header>
                <p class="text-xs leading-5 text-white/60">
                    {{ $address['address_line1'] }} {{ $address['address_line2'] ? ', '.$address['address_line2'] : '' }}<br>
                    {{ $address['city'] }}, {{ $address['province'] }}<br>
                    {{ $address['postal_code'] }}, {{ $address['country'] }}
                </p>
                <footer class="flex items-center gap-3 text-xs text-white/60">
                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 transition hover:bg-white/10 hover:text-white" wire:click="edit({{ $address['id'] }})">
                        Edit
                    </button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 transition hover:bg-rose-500/20 hover:text-rose-200" wire:click="confirmDelete({{ $address['id'] }})">
                        Hapus
                    </button>
                </footer>
            </article>
        @empty
            <div class="glass-panel p-6 text-sm text-white/60">
                Kamu belum menyimpan alamat apapun. Tambahkan alamat baru untuk mempercepat proses checkout.
            </div>
        @endforelse
    </div>

    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-950/60 backdrop-blur">
            <div class="glass-panel w-full max-w-sm space-y-4 p-6 text-center">
                <h3 class="text-lg font-semibold text-white">Hapus alamat?</h3>
                <p class="text-sm text-white/60">Alamat akan dihapus permanen dari akunmu.</p>
                <div class="flex justify-center gap-3 text-sm">
                    <button type="button" class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-white transition hover:bg-white/15" wire:click="delete">Ya, hapus</button>
                    <button type="button" class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-white transition hover:bg-white/15" wire:click="$set('confirmingDelete', false)">Batalkan</button>
                </div>
            </div>
        </div>
    @endif
</x-account.layout>
