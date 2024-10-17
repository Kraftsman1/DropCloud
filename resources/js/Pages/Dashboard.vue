<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    HomeIcon, FilterIcon, InfoIcon, BellIcon, SettingsIcon,
    HeadphonesIcon, FilmIcon, FolderIcon, ImageIcon, FileAudioIcon,
    FileTextIcon
} from 'lucide-vue-next';

const props = defineProps({
    folders: Array,
    files: Array
});

const fileTypes = ref([
    { name: 'Document', checked: false },
    { name: 'Photo', checked: true },
    { name: 'Music', checked: false },
    { name: 'Movie', checked: true },
    { name: 'Spreadsheet', checked: false },
    { name: 'Keynote', checked: false }
]);

const filters = ref([
    { name: 'Me', checked: true },
    { name: 'Team', checked: false }
]);
</script>

<template>
    <AppLayout title="File Manager">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-md">
                <div class="p-4">
                    <img src="/path-to-dropbox-logo.svg" alt="Dropbox" class="w-32">
                </div>
                <nav class="mt-8">
                    <a href="#" class="flex items-center px-4 py-2 text-blue-600 bg-blue-100">
                        <i class="fas fa-home mr-3"></i>
                        Home
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file mr-3"></i>
                        My Files
                    </a>
                    <!-- Add other menu items similarly -->
                </nav>
                <div class="absolute bottom-0 left-0 w-64 p-4">
                    <div class="mb-2">
                        <div class="text-sm text-gray-600">75% In-use</div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500">600GB of 800GB</div>
                    <button class="mt-2 w-full bg-blue-600 text-white py-2 rounded-md">Upgrade</button>
                </div>
            </aside>

            <!-- Main content -->
            <main class="flex-1 p-8 overflow-auto">
                <header class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Cloud</h1>
                        <p class="text-gray-600">Welcome, James Alto! 👋</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <input type="text" placeholder="Search anything here" class="px-4 py-2 border rounded-md">
                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                            <i class="fas fa-bell"></i>
                        </button>
                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                            <i class="fas fa-cog"></i>
                        </button>
                        <img src="/path-to-profile-picture.jpg" alt="Profile" class="w-10 h-10 rounded-full">
                    </div>
                </header>

            </main>

        </div>
    </AppLayout>
</template>