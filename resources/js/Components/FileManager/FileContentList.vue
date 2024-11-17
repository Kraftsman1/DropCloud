<script setup>
import { ref, computed } from "vue";
import { FolderIcon, FileIcon, TrashIcon, DownloadIcon } from "lucide-vue-next";

const props = defineProps({
    contents: {
        type: Object,
        required: true,
        default: () => ({
            data: {
                folders: [],
                files: [],
            },
        }),
    },
});

const data = props.contents ? props.contents.data : { folders: [], files: [] };

const emit = defineEmits(["navigate", "download", "delete"]);

/**
 * Extracts the file name from a given file path.
 */
const extractFileName = (path) => path.split("/").pop();

/**
 * Formats the given file size in bytes.
 */
const formatFileSize = (bytes) => {
    if (!bytes) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

/**
 * Formats a given timestamp into a human-readable date and time string.
 */
const formatDate = (timestamp) => new Date(timestamp * 1000).toLocaleString();

// Handlers
const handleNavigate = (path) => emit("navigate", path);
const handleDownload = (path) => emit("download", path);
const handleDelete = (path, type) => emit("delete", path, type);

</script>

<template>
    <div class="bg-white rounded-lg shadow">
        <!-- Table Header -->
        <div class="grid grid-cols-12 gap-4 p-4 font-semibold text-gray-700 border-b">
            <div class="col-span-6">Name</div>
            <div class="col-span-2">Size</div>
            <div class="col-span-3">Modified</div>
            <div class="col-span-1">Actions</div>
        </div>

        <!-- Directories -->
        <div
            v-for="folder in data.folders"
            :key="folder.path"
            class="grid grid-cols-12 gap-4 p-4 hover:bg-gray-50 border-b cursor-pointer"
            @click="handleNavigate(folder.path)"
        >
            <div class="col-span-6 flex items-center space-x-2">
                <FolderIcon class="w-5 h-5 text-yellow-500" />
                <span>{{ extractFileName(folder.path) }}</span>
            </div>
            <div class="col-span-2">--</div>
            <div class="col-span-3">--</div>
            <div class="col-span-1">
                <button
                    @click.stop="handleDelete(folder.path, 'folder')"
                    class="text-red-500 hover:text-red-700"
                >
                    <TrashIcon class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Files -->
        <div
            v-for="file in data.files"
            :key="file.path"
            class="grid grid-cols-12 gap-4 p-4 hover:bg-gray-50 border-b"
        >
            <div class="col-span-6 flex items-center space-x-2">
                <FileIcon class="w-5 h-5 text-blue-500" />
                <span>{{ extractFileName(file.path) }}</span>
            </div>
            <div class="col-span-2">{{ formatFileSize(file.size) }}</div>
            <div class="col-span-3">{{ formatDate(file.last_modified) }}</div>
            <div class="col-span-1 flex space-x-2">
                <button
                    @click="handleDownload(file.path)"
                    class="text-blue-500 hover:text-blue-700"
                >
                    <DownloadIcon class="w-5 h-5" />
                </button>
                <button
                    @click="handleDelete(file.path, 'file')"
                    class="text-red-500 hover:text-red-700"
                >
                    <TrashIcon class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="!data.files.length && !data.folders.length"
            class="p-8 text-center text-gray-500"
        >
            This folder is empty
        </div>
    </div>
</template>
