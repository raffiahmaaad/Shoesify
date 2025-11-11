<?php

use function Laravel\Folio\name;

name('account.settings-password');

?>

<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-white">{{ __('Password') }}</h2>
        <p class="text-sm text-white/60">{{ __('Update your account password.') }}</p>

        <form class="mt-6 max-w-md space-y-4">
            <div>
                <label class="block text-sm text-white/80">{{ __('Current password') }}</label>
                <input type="password" class="mt-1 block w-full rounded-md bg-zinc-800 text-white/80" />
            </div>
            <div>
                <label class="block text-sm text-white/80">{{ __('New password') }}</label>
                <input type="password" class="mt-1 block w-full rounded-md bg-zinc-800 text-white/80" />
            </div>
            <div>
                <button class="px-4 py-2 rounded bg-primary-600 text-white">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>
