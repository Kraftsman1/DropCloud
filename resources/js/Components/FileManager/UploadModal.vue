<script setup>
import { ref, reactive, computed } from "vue";
import { UploadIcon, XIcon, Trash2Icon } from "lucide-vue-next";

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    provider: {
        type: Object,
        required: true
    },
    currentPath: {
        type: String,
        required: true
    },
});

const emit = defineEmits(["close", "uploaded"]);

const dragOver = ref(false);
const uploading = ref(false);
const state = reactive({
    files: [],
    errorMessage: "",
});

// Configuration
const MAX_FILES = 10;
const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
const ALLOWED_TYPES = [
    "image/jpeg",
    "image/png",
    "image/gif",
    "application/pdf",
    "text/plain",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
];

const validateFile = (file) => {
    if (file.size > MAX_FILE_SIZE) {
        state.errorMessage = `File "${file.name}" exceeds the 50MB size limit.`;
        return false;
    }
    if (!ALLOWED_TYPES.includes(file.type)) {
        state.errorMessage = `File type for "${file.name}" is not supported.`;
        return false;
    }
    return true;
};

const handleDrop = (e) => {
    e.preventDefault();
    dragOver.value = false;
    state.errorMessage = "";

    const droppedFiles = e.dataTransfer?.files;
    if (droppedFiles?.length) {
        addFiles(Array.from(droppedFiles));
    }
};

const handleFileSelect = (e) => {
    const selectedFiles = e.target.files;
    if (selectedFiles?.length) {
        addFiles(Array.from(selectedFiles));
    }
};

const addFiles = (newFiles) => {
    // Filter out already added files and validate new ones
    const uniqueNewFiles = newFiles.filter(
        newFile => !state.files.some(existingFile => existingFile.name === newFile.name)
    );

    const validFiles = uniqueNewFiles.filter(validateFile);

    // Check total files limit
    const totalFiles = state.files.length + validFiles.length;
    if (totalFiles > MAX_FILES) {
        state.errorMessage = `Maximum of ${MAX_FILES} files allowed.`;
        return;
    }

    state.files = [...state.files, ...validFiles];
};

const removeFile = (fileToRemove) => {
    state.files = state.files.filter(file => file !== fileToRemove);
    state.errorMessage = ""; // Clear any previous error messages
};

const handleUpload = async () => {
    if (!state.files.length) return;

    uploading.value = true;
    state.errorMessage = "";

    try {
        const formData = new FormData();
        formData.append("path", props.currentPath);

        state.files.forEach(file => {
            formData.append("files[]", file);
        });

        const response = await axios.post(
            `/file-manager/${props.provider.id}/upload/`,
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        state.files = [];

        showMessage(response.data.message);

        emit("uploaded", props.currentPath);
        emit("close");
    } catch (error) {
        state.errorMessage = `Upload failed: ${error.response?.data?.message || error.message}`;
        console.error("Upload Error:", error);
    } finally {
        uploading.value = false;
    }
};

// Computed for total file size and count
const totalFileSize = computed(() =>
    state.files.reduce((total, file) => total + file.size, 0)
);
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl" role="dialog"
            aria-labelledby="upload-dialog-title" aria-modal="true">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 id="upload-dialog-title" class="text-xl font-bold">
                    Upload Files to {{ currentPath }}
                </h2>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700"
                    aria-label="Close upload modal">
                    <XIcon class="w-6 h-6" />
                </button>
            </div>

            <!-- Upload Area -->
            <div class="border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200" :class="{
                'border-blue-500 bg-blue-50': dragOver,
                'border-gray-300': !dragOver
            }" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop="handleDrop">
                <UploadIcon class="w-12 h-12 mx-auto text-gray-400 mb-4" />

                <div class="mb-4">
                    <p class="text-gray-600">Drag and drop your files here or</p>
                    <label class="text-blue-500 hover:text-blue-700 cursor-pointer">
                        browse
                        <input type="file" class="hidden" @change="handleFileSelect" multiple
                            :accept="ALLOWED_TYPES.join(',')" />
                    </label>
                </div>

                <!-- Selected Files List -->
                <div v-if="state.files.length" class="text-left mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold">Selected Files:</h3>
                        <span class="text-xs text-gray-500">
                            {{ state.files.length }}/{{ MAX_FILES }}
                            ({{ (totalFileSize / (1024 * 1024)).toFixed(2) }}MB)
                        </span>
                    </div>
                    <ul class="space-y-1 max-h-40 overflow-y-auto">
                        <li v-for="file in state.files" :key="file.name"
                            class="flex justify-between items-center text-sm text-gray-600 bg-gray-100 p-2 rounded">
                            <span class="truncate max-w-[70%]">{{ file.name }}</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500">
                                    {{ (file.size / 1024).toFixed(1) }}KB
                                </span>
                                <button @click="removeFile(file)" class="text-red-500 hover:text-red-700"
                                    aria-label="Remove file">
                                    <Trash2Icon class="w-4 h-4" />
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Error Message -->
            <div v-if="state.errorMessage" class="text-red-500 text-sm mt-2">
                {{ state.errorMessage }}
            </div>

            <!-- Upload Information -->
            <div class="text-xs text-gray-500 mt-2">
                <p>Supported file types: PDF, JPEG, PNG, GIF, TXT, DOCX, XLSX</p>
                <p>Maximum file size: 50MB per file</p>
                <p>Maximum total files: {{ MAX_FILES }}</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end mt-4 space-x-2">
                <button @click="$emit('close')" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </button>
                <button @click="handleUpload" :disabled="!state.files.length || uploading"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 transition">
                    {{ uploading ? "Uploading..." : "Upload" }}
                </button>
            </div>
        </div>
    </div>
</template>