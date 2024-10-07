<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { HomeIcon, FilterIcon, InfoIcon, BellIcon, SettingsIcon, 
         HeadphonesIcon, FilmIcon, FolderIcon, ImageIcon, FileAudioIcon, 
         FileTextIcon } from 'lucide-vue-next';

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
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight">
                File Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 text-gray-900 dark:text-gray-100">
                        <nav class="text-sm mb-4">
                            <Link :href="route('dashboard')" class="text-blue-400">
                                <HomeIcon class="inline mr-2" />
                            </Link>
                            / File manager / Work stuff 2022
                        </nav>

                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold">Folders</h2>
                            <div>
                                <button class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-md mr-2">Sort</button>
                                <button class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-md mr-2">View</button>
                                <button class="px-3 py-1 bg-blue-600 text-white rounded-md">+ Create</button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <div v-for="folder in folders" :key="folder.id" class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                <component :is="folder.icon" class="w-12 h-12 mb-2" />
                                <h3 class="font-semibold">{{ folder.name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ folder.fileCount }} · {{ folder.size }}</p>
                            </div>
                        </div>

                        <h2 class="text-xl font-semibold mb-4">Files</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div v-for="file in files" :key="file.id" class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                <img v-if="file.type === 'image'" :src="file.preview" :alt="file.name" class="w-full h-32 object-cover rounded mb-2">
                                <component v-else :is="file.icon" class="w-12 h-12 mb-2" />
                                <h3 class="font-semibold truncate">{{ file.name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ file.size }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>