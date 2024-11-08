<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    HomeIcon,
    FolderIcon,
    FileAudioIcon,
    FileTextIcon,
    FileImageIcon,
    FileVideoIcon,
} from "lucide-vue-next";

const props = defineProps({
    folders: Array,
    files: Array,
});

const getFileTypeIcon = (type) => {
    switch (type) {
        case "document":
            return FileTextIcon;
        case "image":
            return FileImageIcon;
        case "audio":
            return FileAudioIcon;
        case "video":
            return FileVideoIcon;
        default:
            return FolderIcon;
    }
};

const formatFileSize = (size) => {
    const units = ["B", "KB", "MB", "GB", "TB"];
    let i = 0;
    while (size >= 1024 && i < units.length - 1) {
        size /= 1024;
        i++;
    }
    return `${size.toFixed(2)} ${units[i]}`;
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString();
};
</script>

<template>
    <AppLayout title="File Manager">

            <!-- Main content -->
            <main class="flex-1 p-8 overflow-auto">
                <header class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            Welcome, {{ $page.props.auth.user.name }}! 👋
                        </h1>
                    </div>
                </header>

                <section>
                    <div
                        class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt
                                    class="text-sm font-medium text-gray-500 truncate"
                                >
                                    Total Storage
                                </dt>
                                <dd
                                    class="mt-1 text-3xl font-semibold text-gray-900"
                                >
                                    250.5 GB
                                </dd>
                                <div class="mt-1 text-sm text-gray-500">
                                    +20% from last month
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt
                                    class="text-sm font-medium text-gray-500 truncate"
                                >
                                    Active Teams
                                </dt>
                                <dd
                                    class="mt-1 text-3xl font-semibold text-gray-900"
                                >
                                    7
                                </dd>
                                <div class="mt-1 text-sm text-gray-500">
                                    +2 new this week
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt
                                    class="text-sm font-medium text-gray-500 truncate"
                                >
                                    Storage Providers
                                </dt>
                                <dd
                                    class="mt-1 text-3xl font-semibold text-gray-900"
                                >
                                    3
                                </dd>
                                <div class="mt-1 text-sm text-gray-500">
                                    AWS S3, GCS, Azure
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dt
                                    class="text-sm font-medium text-gray-500 truncate"
                                >
                                    Usage Overview
                                </dt>
                                <dd
                                    class="mt-1 text-3xl font-semibold text-gray-900"
                                >
                                    75%
                                </dd>
                                <div class="mt-1 text-sm text-gray-500">
                                    Of total storage used
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <!-- Recent Activity -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-lg font-semibold mb-4">
                                Recent Activity
                            </h2>
                            <p class="text-sm text-gray-600">
                                John uploaded file.pdf to Project X
                            </p>
                            <p class="text-sm text-gray-600">
                                Sarah shared folder "Designs" with Marketing
                                team
                            </p>
                            <p class="text-sm text-gray-600">
                                New team "Product Launch" created
                            </p>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-lg font-semibold mb-4">
                                Quick Actions
                            </h2>
                            <Link :href="route('storage-providers.create')">
                                <button
                                    class="w-full bg-black text-white py-2 rounded-lg mb-2"
                                >
                                    + Add Storage Provider
                                </button>
                            </Link>
                            <Link :href="route('storage-providers.index')">
                                <button
                                    class="w-full bg-blue-600 text-white py-2 rounded-lg mb-2"
                                >
                                    + View Storage Providers
                                </button>
                            </Link>

                            <button
                                class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg mb-2"
                            >
                                Manage Teams
                            </button>
                            <button
                                class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg"
                            >
                                Configure Storage
                            </button>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex justify-between items-center mt-8 mb-4">
                        <h2 class="text-xl font-semibold">All files</h2>
                        <button
                            class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md"
                        >
                            + Add new
                        </button>
                    </div>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <!-- Repeat this card for each folder -->
                        <div
                            v-for="folder in props.folders"
                            :key="folder.id"
                            class="bg-white p-4 rounded-lg shadow"
                        >
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center">
                                    <FolderIcon class="mr-2" />
                                    <span class="font-medium">{{
                                        folder.name
                                    }}</span>
                                </div>
                                <button
                                    class="text-gray-400 hover:text-gray-600"
                                >
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                            <div
                                class="flex justify-between text-sm text-gray-600"
                            >
                                <div>
                                    <span>Shared Users</span>
                                    <div
                                        class="flex -space-x-2 overflow-hidden mt-1"
                                    >
                                        <!-- Repeat for each user avatar -->
                                        <img
                                            v-for="user in folder.sharedUsers"
                                            :key="user.id"
                                            class="inline-block h-6 w-6 rounded-full ring-2 ring-white"
                                            :src="user.avatarUrl"
                                            :alt="user.name"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <span>Inside Files</span>
                                    <p class="text-blue-500 font-medium mt-1">
                                        {{ folder.fileCount }}
                                    </p>
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
                            <tr
                                v-for="file in props.files"
                                :key="file.id"
                                class="border-b"
                            >
                                <td class="py-3 flex items-center">
                                    <component
                                        :is="getFileTypeIcon(file.type)"
                                        class="mr-2"
                                    />
                                    {{ file.name }}
                                </td>
                                <td>
                                    <div
                                        class="flex -space-x-2 overflow-hidden"
                                    >
                                        <!-- Repeat for each user avatar -->
                                        <img
                                            v-for="user in file.sharedUsers"
                                            :key="user.id"
                                            class="inline-block h-6 w-6 rounded-full ring-2 ring-white"
                                            :src="user.avatarUrl"
                                            :alt="user.name"
                                        />
                                    </div>
                                </td>
                                <td>{{ formatFileSize(file.size) }}</td>
                                <td>{{ formatDate(file.lastModified) }}</td>
                                <td>
                                    <button
                                        class="text-gray-400 hover:text-gray-600"
                                    >
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </main>
    </AppLayout>
</template>
