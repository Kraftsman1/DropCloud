<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    provider: {
        type: Object,
        required: true,
    },
    providers: {
        type: Array,
        default: () => [],
    },
    contents: {
        type: Array,
        required: true,
    },
    error: {
        type: String,
        default: null,
    },
});

// methods
/**
 * Extracts the file name from a given file path.
 *
 * @param {string} path - The full file path.
 * @returns {string} The extracted file name.
 */
const extractFileName = (path) => {
    return path.split("/").pop();
};

/**
 * Formats the given file size in bytes to a string representation in megabytes (MB).
 *
 * @param {number} size - The file size in bytes.
 * @returns {string} The formatted file size in megabytes with two decimal places followed by " MB".
 */
const formatFileSize = (size) => {
    return (size / (1024 * 1024)).toFixed(2) + " MB";
};

/**
 * Formats a given timestamp into a human-readable date and time string.
 *
 * @param {number|string|Date} timestamp - The timestamp to format. Can be a number, string, or Date object.
 * @returns {string} The formatted date and time string.
 */
const formatTimestamp = (timestamp) => {
    return new Date(timestamp).toLocaleString();
};

// Watch for path changes
// watch(currentPath, async (newPath) => {
//     try {
//         const response = await axios.get("/api/filemanager/list", {
//             params: {
//                 provider: props.provider.id,
//                 path: newPath,
//             },
//         });
//         if (response.data.success) {
//             currentContents.value = response.data.data;
//         }
//     } catch (error) {
//         console.error("Failed to fetch contents:", error);
//     }
// });
</script>

<template>
    <div class="p-4">
        <!-- Header -->
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                {{ provider.name }} - File Manager
            </h1>
            <div class="space-x-2">
                <button
                    @click="isCreatingFolder = true"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    New Folder
                </button>
                <button
                    @click="isUploading = true"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                >
                    Upload File
                </button>
            </div>
        </div>

        <!-- Breadcrumb Navigation -->
        <BreadcrumbNavigation :path="currentPath" @navigate="navigateTo" />

        <!-- File List -->
        <!-- <FileList
            :contents="currentContents"
            :selected-items="selectedItems"
            @navigate="navigateTo"
            @download="downloadFile"
            @delete="deleteItem"
        /> -->
        
        <!-- File Contents List -->
        <div v-if="contents && contents.length">
            <h3>Files</h3>
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Size (MB)</th>
                        <th>Last Modified</th>
                        <th>Storage Class</th>
                        <th>ETag</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="file in contents" :key="file.path">
                        <td>{{ extractFileName(file.path) }}</td>
                        <td>{{ formatFileSize(file.file_size) }}</td>
                        <td>{{ formatTimestamp(file.last_modified) }}</td>
                        <td>{{ file.extra_metadata.StorageClass }}</td>
                        <td>{{ file.extra_metadata.ETag }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No files found.</p>
        </div>

        <!-- Upload Modal -->
        <!-- <UploadModal
            v-if="isUploading"
            :current-path="currentPath"
            @close="isUploading = false"
            @upload="handleUpload"
        /> -->

        <!-- Create Folder Modal -->
        <!-- <CreateFolderModal
            v-if="isCreatingFolder"
            @close="isCreatingFolder = false"
            @create="createDirectory"
        /> -->
    </div>
</template>
