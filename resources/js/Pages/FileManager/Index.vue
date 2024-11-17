<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import FileContentList from "@/Components/FileManager/FileContentList.vue";

const props = defineProps({
    provider: {
        type: Object,
        required: true,
    },
    contents: {
        type: Object,
        required: true,
    },
    error: {
        type: String,
        default: null,
    },
});

const currentPath = ref("/");
const currentContents = ref(props.contents);

const isCreatingFolder = ref(false);
const isUploading = ref(false);
const selectedItems = ref([]);

const navigateTo = async (path) => {
    router.get(`/file-manager/${props.provider.id}/${encodeURIComponent(path)}`);
}

const download = (path) => {
    if (props.provider && props.provider.id) {
        const url = `/file-manager/${props.provider.id}/download/${encodeURIComponent(path)}`;
        // Opens the download in a new tab
        window.open(url, '_blank');
    } else {
        console.error("Provider is not defined or does not have an ID.");
    }
};

const handleDelete = async (path, type) => {
    try {
        // Confirmation before deletion
        if (!confirm(`Are you sure you want to delete this ${type}?`)) {
            return;
        }

        const encodedPath = encodeURIComponent(path);

        // Wrap the deletion request inside a try-catch
        try {
            const response = await axios.delete(`/file-manager/${props.provider.id}/delete/${encodedPath}`);
            console.log("Delete response:", response.status);

            if (response.status === 200) {
                alert(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully.`);

                // Update the file list to reflect the deletion
                router.get(`/file-manager/${props.provider.id}`, {}, {
                    replace: true,
                    preserveState: true,
                });
            } else {
                alert(`Failed to delete ${type}.`);
            }
        } catch (deleteError) {
            console.error("Delete request failed:", deleteError);
            alert(`Failed to delete ${type}.`);
        }

    } catch (error) {
        console.error("Delete error:", error);
        alert(`An error occurred while deleting the ${type}.`);
    }
};

watch(() => props.contents, (newContents) => {
    currentContents.value = newContents;
});

</script>

<template>
    <AppLayout title="File Manager">
        <main class="flex-1 p-8 overflow-auto">
            <!-- Header -->
            <div class="mb-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    {{ provider.name }} - File Manager
                </h1>
                <div class="space-x-2">
                    <button @click="isCreatingFolder = true"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        New Folder
                    </button>
                    <button @click="isUploading = true"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Upload File
                    </button>
                </div>
            </div>

            <!-- Breadcrumb Navigation -->
            <!-- <BreadcrumbNavigation :path="currentPath" @navigate="navigateTo" /> -->

            <!-- File List -->

            <div v-if="contents">
                <FileContentList :contents="currentContents" @navigate="navigateTo" @download="download"
                    @delete="handleDelete" />
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
        </main>
    </AppLayout>
</template>
