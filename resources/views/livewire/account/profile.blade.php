<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $gender = null;
    public ?string $birth_date = null;
    public ?string $bio = null;
    public $avatar;
    public ?string $avatarPreview = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->gender = $user->gender;
        $this->birth_date = optional($user->birth_date)?->format('Y-m-d');
        $this->bio = $user->bio;
        $this->avatarPreview = $user->avatar_path ? Storage::disk('public')->url($user->avatar_path) : null;
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);
        $this->avatarPreview = $this->avatar?->temporaryUrl();
    }

    public function save(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'max:20', Rule::in(['male', 'female', 'non-binary', 'prefer_not_to_say', null])],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $validated['avatar_path'] = $path;
            $this->avatarPreview = Storage::disk('public')->url($path);
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'bio' => $validated['bio'],
        ]);

        if (isset($validated['avatar_path'])) {
            $user->avatar_path = $validated['avatar_path'];
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-saved');
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->forceFill([
            'avatar_path' => null,
        ])->save();

        $this->avatar = null;
        $this->avatarPreview = null;
    }
}; ?>

<x-account.layout title="Profil & Preferensi" subtitle="Kelola identitas dan preferensi agar pengalaman belanja semakin personal.">
    <div class="glass-panel grid gap-8 p-6 md:grid-cols-[220px_minmax(0,1fr)]">
        <div class="flex flex-col items-center gap-4 text-center">
            <div class="relative">
                @if ($avatarPreview)
                    <img src="{{ $avatarPreview }}" alt="Avatar" class="h-32 w-32 rounded-full object-cover shadow-[0_18px_60px_rgba(5,15,33,0.45)]">
                @else
                    <div class="flex h-32 w-32 items-center justify-center rounded-full border border-dashed border-white/20 bg-white/5 text-3xl font-semibold text-white/70">
                        {{ auth()->user()->initials() }}
                    </div>
                @endif
                <label class="absolute bottom-0 right-2 inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-white/10 bg-white/10 text-white shadow-[0_12px_30px_rgba(5,15,33,0.45)] transition hover:bg-white/20">
                    <input type="file" class="hidden" wire:model="avatar" accept="image/*">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 6.75h3l1.5-2.25h6l1.5 2.25h3a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5h-15a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5Zm7.5 3v5m0 0 2.5-2.5M12 14.75l-2.5-2.5" />
                    </svg>
                </label>
            </div>
            <div class="space-y-1">
                <p class="text-sm text-white/60">Gunakan foto formal untuk memudahkan tim CS mengenalimu.</p>
                @if ($avatarPreview)
                    <button type="button" class="text-xs text-rose-300 transition hover:text-rose-200" wire:click="removeAvatar">Hapus foto</button>
                @endif
            </div>
            @error('avatar') <p class="text-xs text-rose-300">{{ $message }}</p> @enderror
        </div>

        <form wire:submit="save" class="space-y-5 text-sm text-white/80">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Nama lengkap</span>
                    <input type="text" wire:model.defer="name" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('name') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Email</span>
                    <input type="email" wire:model.defer="email" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('email') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">No. telepon</span>
                    <input type="text" wire:model.defer="phone" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30" placeholder="+62">
                    @error('phone') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Tanggal lahir</span>
                    <input type="date" wire:model.defer="birth_date" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                    @error('birth_date') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">Gender</span>
                    <select wire:model.defer="gender" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                        <option value="">Pilih</option>
                        <option value="male">Pria</option>
                        <option value="female">Wanita</option>
                        <option value="non-binary">Non-biner</option>
                        <option value="prefer_not_to_say">Tidak ingin disebutkan</option>
                    </select>
                    @error('gender') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="space-y-2 block">
                <span class="text-xs uppercase tracking-[0.3em] text-white/40">Bio singkat</span>
                <textarea wire:model.defer="bio" rows="3" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30" placeholder="Bagikan minat sneaker atau gaya favoritmu."></textarea>
                @error('bio') <span class="text-xs text-rose-300">{{ $message }}</span> @enderror
            </label>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50" wire:loading.attr="disabled">
                    Simpan perubahan
                </button>
                <div class="text-xs text-white/50" wire:loading>Memproses...</div>
                <div class="text-xs text-emerald-300" wire:loading.remove wire:target="save" x-data x-on:profile-saved.window="$el.textContent='Profil berhasil diperbarui'; setTimeout(()=> $el.textContent='',3000)"></div>
            </div>
        </form>
    </div>
</x-account.layout>
