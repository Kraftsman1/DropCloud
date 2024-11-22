<!-- FileManager.vue -->
<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import FileContentList from "@/Components/FileManager/FileContentList.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ActionMessage from "@/Components/ActionMessage.vue";

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

// State Management with more descriptive names
const currentPath = ref("/");
const currentContents = ref(props.contents);
const selectedItems = ref([]);
const actionMessage = ref(null);
const messageTimeout = ref(null);

// Modal States
const showDeleteModal = ref(false);
const showCreateFolderModal = ref(false);
const showUploadModal = ref(false);

// Active item and loading state
const activeItem = ref(null);
const isLoading = ref(false);

// Composable functions for better organization
const showActionMessage = (message, type = 'success') => {
    actionMessage.value = {
        text: message,
        type: type,
    };

    if (messageTimeout.value) {
        clearTimeout(messageTimeout.value);
    }

    messageTimeout.value = setTimeout(() => {
        actionMessage.value = null;
    }, 5000);
};

const updateContents = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(
            `/file-manager/${props.provider.id}${currentPath.value}`
        );
        currentContents.value = response.data.contents;
    } catch (error) {
        showActionMessage(`Failed to refresh contents: ${error.message}`, "error");
        console.error("Error fetching contents:", error);
    } finally {
        isLoading.value = false;
    }
};

const navigateTo = (path) => {
    currentPath.value = path;
    router.get(`/file-manager/${props.provider.id}${path}`);
};

const download = (path) => {
    if (!props.provider?.id) {
        showActionMessage("Provider information is missing", "error");
        return;
    }
    
    const url = `/file-manager/${props.provider.id}/download/${encodeURIComponent(path)}`;
    window.open(url, '_blank');
};

const handleDelete = async () => {
    if (!activeItem.value) return;

    try {
        const encodedPath = encodeURIComponent(activeItem.value.path);
        const response = await axios.delete(
            `/file-manager/${props.provider.id}/delete/${encodedPath}`
        );

        if (response.status === 200) {
            showActionMessage(`${activeItem.value.type} deleted successfully`);
            await updateContents();
            // Clear selection if deleted item was selected
            selectedItems.value = selectedItems.value.filter(
                item => item.path !== activeItem.value.path
            );
        }
    } catch (error) {
        showActionMessage(
            `Failed to delete ${activeItem.value.type}: ${error.message}`,
            "error"
        );
    } finally {
        showDeleteModal.value = false;
        activeItem.value = null;
    }
};

const confirmDelete = (item) => {
    activeItem.value = item;
    showDeleteModal.value = true;
};

// Cleanup
// onBeforeUnmount(() => {
//     if (messageTimeout.value) {
//         clearTimeout(messageTimeout.value);
//     }
// });

// Watch for contents changes
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
                    <button
                        @click="showCreateFolderModal = true"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
                    >
                        New Folder
                    </button>
                    <button
                        @click="showUploadModal = true"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"
                    >
                        Upload File
                    </button>
                </div>
            </div>

            <!-- Action Message -->
            <ActionMessage
                v-if="actionMessage"
                :message="actionMessage.text"
                :type="actionMessage.type"
            />

            <!-- Loading State -->
            <div
                v-if="isLoading"
                class="flex justify-center items-center py-8"
            >
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500" />
            </div>

            <!-- File List -->
            <div v-if="currentContents">
                <FileContentList
                    :contents="currentContents"
                    :selected-items="selectedItems"
                    @navigate="navigateTo"
                    @download="download"
                    @delete="confirmDelete"
                    @select="item => selectedItems.push(item)"
                    @deselect="item => selectedItems = selectedItems.filter(i => i.path !== item.path)"
                />
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                No files found.
            </div>

            <!-- Delete Confirmation Modal -->
            <ConfirmationModal
                :show="showDeleteModal"
                @close="showDeleteModal = false"
                @confirm="handleDelete"
            >
                <template #title>
                    Confirm Delete
                </template>
                <template #content>
                    Are you sure you want to delete 
                    <span class="font-semibold">{{ activeItem?.name }}</span>?
                    This action cannot be undone.
                </template>
                <template #footer>
                    <button
                        @click="handleDelete"
                        class="bg-red-600 text-white px-4 py-2 rounded mr-2 hover:bg-red-700 transition"
                    >
                        Delete
                    </button>
                    <button
                        @click="showDeleteModal = false"
                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 transition"
                    >
                        Cancel
                    </button>
                </template>
            </ConfirmationModal>
        </main>
    </AppLayout>
</template>