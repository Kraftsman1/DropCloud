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
                        <p class="text-gray-600">Welcome, Deen! 👋</p>
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

                <section>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">All files</h2>
                        <button class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md">+ Add new</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Repeat this card for each folder -->
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-folder text-blue-500 mr-2"></i>
                                    <span class="font-medium">Documents</span>
                                </div>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <div>
                                    <span>Shared Users</span>
                                    <div class="flex -space-x-2 overflow-hidden mt-1">
                                        <!-- Repeat for each user avatar -->
                                        <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white"
                                            src="/path-to-avatar.jpg" alt="">
                                    </div>
                                </div>
                                <div>
                                    <span>Inside Files</span>
                                    <p class="text-blue-500 font-medium mt-1">3,985</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Recent Files</h2>
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-600 border-b">
                                <th class="pb-2">Name</th>
                                <th class="pb-2">Shared Users</th>
                                <th class="pb-2">File Size</th>
                                <th class="pb-2">Last Modified</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Repeat this row for each recent file -->
                            <tr class="border-b">
                                <td class="py-3 flex items-center">
                                    <i class="fas fa-file-image text-orange-500 mr-2"></i>
                                    Website Design.png
                                </td>
                                <td>
                                    <div class="flex -space-x-2 overflow-hidden">
                                        <!-- Repeat for each user avatar -->
                                        <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white"
                                            src="/path-to-avatar.jpg" alt="">
                                    </div>
                                </td>
                                <td>2.8 MB</td>
                                <td>Dec 13, 2022</td>
                                <td>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

            </main>

        </div>
    </AppLayout>
</template>