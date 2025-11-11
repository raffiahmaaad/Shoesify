<?php

use function Laravel\Folio\name;

name('account.settings-two-factor');

?>

<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-white">{{ __('Two-Factor Authentication') }}</h2>
        <p class="text-sm text-white/60">{{ __('Manage your two-factor authentication settings.') }}</p>

        <div class="mt-6">
            <p class="text-sm text-white/60">{{ __('Two-factor setup will appear here.') }}</p>
        </div>
    </div>
</div>
