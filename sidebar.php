<?php
// Get the current page from the URL query parameter.
// Default to an empty string if not set to prevent errors.
$current_page = $_GET['page'] ?? '';
?>
<div class="w-64 bg-slate-900 text-white shadow-lg rounded-r-lg flex flex-col p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Dashboard</h1>
    </div>

    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href="?page=konkan-overview" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'konkan-overview' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Konkan renge Overview
                </a>
            </li>
            <li>
                <a href="?page=konkan-map" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'konkan-map' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Konkan renge Map
                </a>
            </li>
        </ul>

        <h2 class="mt-8 mb-2 text-xs font-semibold uppercase text-slate-400">DISTRICTS</h2>
        <ul class="space-y-2">
            <li>
                <a href="?page=palghar" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'palghar' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Palghar
                </a>
            </li>
            <li>
                <a href="?page=thane-rural" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'thane-rural' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Thane Rural
                </a>
            </li>
            <li>
                <a href="?page=raigad" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'raigad' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Raigad
                </a>
            </li>
            <li>
                <a href="?page=ratnagiri" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'ratnagiri' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Ratnagiri
                </a>
            </li>
            <li>
                <a href="?page=sindhudurg" class="block p-3 rounded-lg text-sm transition-colors
                    <?= $current_page == 'sindhudurg' ? 'bg-slate-700 text-white' : 'hover:bg-slate-700 text-slate-300' ?>">
                    Sindhudurg
                </a>
            </li>
        </ul>
    </nav>
</div>