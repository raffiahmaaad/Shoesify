<?php

use function Laravel\Folio\name;

name('account.settings');

?>

<x-account.layout title="Settings" subtitle="Manage your profile and account settings">
    <div class="glass-panel grid gap-8 p-6 md:grid-cols-[220px_minmax(0,1fr)]">
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

        <div class="md:col-span-1">
            <div class="text-sm text-white/60">
                <p>{{ __('Choose an item on the left to edit its settings.') }}</p>
            </div>
        </div>
    </div>
</x-account.layout>
