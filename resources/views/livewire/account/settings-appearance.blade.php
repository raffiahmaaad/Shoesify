<?php

use function Laravel\Folio\name;

name('account.settings-appearance');

?>

<x-account.layout title="Appearance" subtitle="Customize your interface preferences.">
    <div class="glass-panel grid gap-8 p-6 md:grid-cols-[220px_minmax(0,1fr)]">
        <!-- Left Column: Navigation -->
        <nav class="flex flex-col gap-3">
            @php
                $tabs = [
                    ['label' => __('Profile'), 'route' => 'account.profile'],
                    ['label' => __('Password'), 'route' => 'account.settings.password'],
                    ['label' => __('Appearance'), 'route' => 'account.settings.appearance'],
                ];
            @endphp

            @foreach ($tabs as $tab)
                @php $isActive = request()->routeIs($tab['route']) || request()->routeIs($tab['route'] . '*'); @endphp
                <a href="{{ route($tab['route']) }}" wire:navigate @class([
                    'inline-flex items-center justify-between gap-3 rounded-2xl px-4 py-3 transition',
                    'bg-[#4de4d4]/15 text-white' => $isActive,
                    'bg-white/5 hover:bg-white/10 text-white/70' => !$isActive,
                ])>
                    <span>{{ $tab['label'] }}</span>
                    @if ($isActive)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 5 7 7-7 7" />
                        </svg>
                    @endif
                </a>
            @endforeach

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                @php $twoActive = request()->routeIs('account.settings.two-factor') || request()->routeIs('account.settings.two-factor.*'); @endphp
                <a href="{{ route('account.settings.two-factor') }}" wire:navigate @class([
                    'inline-flex items-center justify-between gap-3 rounded-2xl px-4 py-3 transition',
                    'bg-[#4de4d4]/15 text-white' => $twoActive,
                    'bg-white/5 hover:bg-white/10 text-white/70' => !$twoActive,
                ])>
                    <span>{{ __('Two-Factor Auth') }}</span>
                    @if ($twoActive)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9 5 7 7-7 7" />
                        </svg>
                    @endif
                </a>
            @endif
        </nav>

        <!-- Right Column: Settings Form -->
        <form wire:submit="save" class="space-y-5 text-sm text-white/80">
            <div class="space-y-4">
                <!-- Theme -->
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">{{ __('Theme') }}</span>
                    <select wire:model="theme"
                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                        <option value="light">{{ __('Light') }}</option>
                        <option value="dark">{{ __('Dark') }}</option>
                        <option value="system">{{ __('System') }}</option>
                    </select>
                </label>

                <!-- Font Size -->
                <label class="space-y-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-white/40">{{ __('Font size') }}</span>
                    <select wire:model="fontSize"
                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-[#4de4d4]/60 focus:outline-none focus:ring-2 focus:ring-[#4de4d4]/30">
                        <option value="small">{{ __('Small') }}</option>
                        <option value="medium">{{ __('Medium') }}</option>
                        <option value="large">{{ __('Large') }}</option>
                    </select>
                </label>
            </div>

            <!-- Save Button -->
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-[#4de4d4] px-6 py-3 text-sm font-semibold text-neutral-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50"
                    wire:loading.attr="disabled">
                    {{ __('Save changes') }}
                </button>
                <div class="text-xs text-white/50" wire:loading>{{ __('Processing...') }}</div>
                <div class="text-xs text-emerald-300" wire:loading.remove wire:target="save" x-data
                    x-on:appearance-saved.window="$el.textContent='Settings saved successfully'; setTimeout(()=> $el.textContent='',3000)">
                </div>
            </div>
        </form>
    </div>
</x-account.layout>
