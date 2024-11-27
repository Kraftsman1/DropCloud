<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import FileContentList from "@/Components/FileManager/FileContentList.vue";
import UploadModal from "@/Components/FileManager/UploadModal.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ActionMessage from "@/Components/ActionMessage.vue";

// Props
const props = defineProps({
    provider: { 
        type: Object, 
        required: true 
    },
    contents: { 
        type: Object, 
        required: true 
    },
    error: { 
        type: String, 
        default: null 
    },
});

// State Management
const currentPath = ref(props.contents?.data?.path || "/");
const currentContents = ref(props.contents);
const selectedItems = ref([]);
const actionMessage = ref(null);
const isLoading = ref(false);
const isDeleting = ref(false);

// Modal States
const showDeleteModal = ref(false);
const showCreateFolderModal = ref(false);
const showUploadModal = ref(false);

// Active item
const activeItem = ref(null);

// Helper Functions
const showMessage = (text, type = "success") => {
    actionMessage.value = { text, type };
};

const clearMessage = () => {
    actionMessage.value = null;
};

const navigateTo = (path) => {
    currentPath.value = path;
    router.get(`/file-manager/${props.provider.id}/${path}`);
};

const download = (path) => {
    if (!props.provider?.id) {
        showMessage("Provider information is missing", "error");
        return;
    }
    const url = `/file-manager/${props.provider.id}/download/${encodeURIComponent(path)}`;
    window.open(url, "_blank");
};

const handleDelete = async () => {
    if (!activeItem.value || !props.provider?.id) {
        showMessage("No item selected or provider information missing.", "error");
        return;
    }

    try {
        isDeleting.value = true;
        const encodedPath = encodeURIComponent(activeItem.value.path);
        await axios.delete(`/file-manager/${props.provider.id}/delete/${encodedPath}`);

        // Close the modal immediately
        showDeleteModal.value = false;

        showMessage(`${activeItem.value.name} deleted successfully`);

        // Remove the deleted item from selectedItems
        selectedItems.value = selectedItems.value.filter(
            (item) => item.path !== activeItem.value.path
        );

        location.reload();


    } catch (error) {
        showMessage(`Failed to delete ${activeItem.value.name}: ${error.message}`, "error");
        console.error("Delete Error:", error);
    } finally {
        isDeleting.value = false;
        activeItem.value = null;
    }
};

const handleUploadSuccess = (response) => {
    showMessage(response);
    navigateTo(currentPath.value);
};

const handleSuccessResponse = (response) => {
    showMessage(response);
};

const confirmDelete = (item) => {
    activeItem.value = item;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    activeItem.value = null;
};

// Watch for `props.contents` changes
watch(
    () => props.contents,
    (newContents) => {
        currentContents.value = newContents;
    }
);

// Debugging (Optional)
watch(currentPath, (newPath) => {
    console.log("Current path updated to:", newPath);
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
                    <button @click="showCreateFolderModal = true"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                        New Folder
                    </button>
                    <button @click="showUploadModal = true"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                        Upload File
                    </button>
                </div>
            </div>

            <!-- Action Message -->
            <ActionMessage v-if="actionMessage" :message="actionMessage.text" :type="actionMessage.type"
                @close="clearMessage" />

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>

            <!-- File List -->
            <div v-if="currentContents">
                <FileContentList :contents="currentContents" :selected-items="selectedItems" @navigate="navigateTo"
                    @download="download" @delete="confirmDelete" @select="(item) => selectedItems.push(item)" @deselect="(item) =>
                        (selectedItems = selectedItems.filter((i) => i.path !== item.path))" />
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                No files found.
            </div>

            <!-- Delete Confirmation Modal -->
            <ConfirmationModal :show="showDeleteModal" @close="closeDeleteModal">
                <template #title>Confirm Delete</template>
                <template #content>
                    Are you sure you want to delete
                    <span class="font-semibold">{{ activeItem?.name }}</span>?
                    This action cannot be undone.
                </template>
                <template #footer>
                    <button @click="handleDelete" :disabled="isDeleting"
                        class="bg-red-600 text-white px-4 py-2 rounded mr-2 hover:bg-red-700 transition disabled:opacity-50">
                        {{ isDeleting ? "Deleting..." : "Delete" }}
                    </button>
                    <button @click="closeDeleteModal"
                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 transition">
                        Cancel
                    </button>
                </template>
            </ConfirmationModal>

            <!-- Upload Modal -->
            <UploadModal 
                :show="showUploadModal" 
                :provider="provider"
                :currentPath="currentPath"
                @close="showUploadModal = false" 
                @uploaded="handleUploadSuccess"
                @response="handleSuccessResponse" />

        </main>
    </AppLayout>
</template>