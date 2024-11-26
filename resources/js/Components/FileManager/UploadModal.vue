<script setup>
import { ref, reactive, computed, watch } from "vue";
import { UploadIcon, XIcon, Trash2Icon, ImageIcon } from "lucide-vue-next";
import axios from "axios";

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

const emit = defineEmits(["close", "uploaded", "response"]);

// State management
const dragOver = ref(false);
const uploading = ref(false);
const state = reactive({
    files: [],
    errorMessages: [],
    uploadProgress: {},
    cancelTokens: {}
});

// Configuration
const MAX_FILES = 10;
const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
const ALLOWED_TYPES = {
    'image/jpeg': 'JPEG',
    'image/png': 'PNG', 
    'image/gif': 'GIF',
    'application/pdf': 'PDF',
    'text/plain': 'TXT',
    'application/msword': 'DOC',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
    'application/vnd.ms-excel': 'XLS',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX'
};

// Generate file preview
const generateFilePreview = (file) => {
    return new Promise((resolve) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                resolve(e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            resolve(null);
        }
    });
};

// Enhanced validation with multiple error messages
const validateFile = async (file) => {
    const errors = [];

    if (file.size > MAX_FILE_SIZE) {
        errors.push(`"${file.name}": Exceeds 50MB size limit`);
    }

    if (!ALLOWED_TYPES[file.type]) {
        errors.push(`"${file.name}": Unsupported file type`);
    }

    // Optional: Add more sophisticated validation
    const preview = file.type.startsWith('image/') ? await generateFilePreview(file) : null;

    return {
        file,
        preview,
        errors
    };
};

const handleDrop = async (e) => {
    e.preventDefault();
    dragOver.value = false;
    state.errorMessages = [];

    const droppedFiles = e.dataTransfer?.files;
    if (droppedFiles?.length) {
        await addFiles(Array.from(droppedFiles));
    }
};

const handleFileSelect = async (e) => {
    const selectedFiles = e.target.files;
    if (selectedFiles?.length) {
        await addFiles(Array.from(selectedFiles));
    }
};

const addFiles = async (newFiles) => {
    state.errorMessages = [];

    // Validate and process files
    const processedFiles = await Promise.all(
        newFiles.map(validateFile)
    );

    // Collect errors
    const fileErrors = processedFiles.flatMap(f => f.errors);
    if (fileErrors.length) {
        state.errorMessages = fileErrors;
        return;
    }

    // Filter out duplicates and validate total files
    const uniqueNewFiles = processedFiles.filter(
        newFile => !state.files.some(
            existingFile => existingFile.file.name === newFile.file.name
        )
    );

    const totalFiles = state.files.length + uniqueNewFiles.length;
    if (totalFiles > MAX_FILES) {
        state.errorMessages.push(`Maximum of ${MAX_FILES} files allowed`);
        return;
    }

    state.files = [...state.files, ...uniqueNewFiles];
};

const removeFile = (fileToRemove) => {
    // Cancel ongoing upload if exists
    if (state.cancelTokens[fileToRemove.file.name]) {
        state.cancelTokens[fileToRemove.file.name].cancel('Upload cancelled by user');
    }

    state.files = state.files.filter(file => file !== fileToRemove);
    delete state.uploadProgress[fileToRemove.file.name];
    delete state.cancelTokens[fileToRemove.file.name];
    state.errorMessages = [];
};

const handleUpload = async () => {

    if (!props.provider?.id) {
        state.errorMessages.push("Provider information is missing");
        return;
    }

    if (!state.files.length) return;

    uploading.value = true;
    state.errorMessages = [];
    state.uploadProgress = {}; // Reset progress

    try {
        const formData = new FormData();
        formData.append("providerId", props.provider.id);
        formData.append("path", props.currentPath || '/');


        state.files.forEach((fileObj) => {
            formData.append("files[]", fileObj.file);
            // Initialize progress for each file
            state.uploadProgress[fileObj.file.name] = 0;
        });

        const response = await axios.post(`/file-manager/${props.provider.id}/upload/${props.currentPath}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
            onUploadProgress: (progressEvent) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                
                // Update overall progress for each file
                state.files.forEach(fileObj => {
                    state.uploadProgress[fileObj.file.name] = percentCompleted;
                });
            }
        });

        state.files = [];
        emit("uploaded", props.currentPath);
        emit("response", response.data.message);
        emit("close");
    } catch (error) {
        const errorMessage = error.response?.data?.error 
            || error.response?.data?.message 
            || error.message 
            || 'Unknown upload error';

        state.errorMessages.push(`Upload failed: ${errorMessage}`);
        console.error('Upload Error:', error);
    } finally {
        uploading.value = false;
        state.uploadProgress = {};
    }
};

// Computed properties
const totalFileSize = computed(() =>
    state.files.reduce((total, fileObj) => total + fileObj.file.size, 0)
);

const overallUploadProgress = computed(() => {
    const progresses = Object.values(state.uploadProgress);
    return progresses.length 
        ? progresses.reduce((a, b) => a + b, 0) / progresses.length 
        : 0;
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full shadow-xl" role="dialog"
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
                            :accept="Object.keys(ALLOWED_TYPES).join(',')" />
                    </label>
                </div>

                <!-- Error Messages -->
                <div v-if="state.errorMessages.length" class="text-red-500 text-sm mb-4">
                    <p v-for="(error, index) in state.errorMessages" :key="index">
                        {{ error }}
                    </p>
                </div>

                <!-- Selected Files List -->
<!-- Selected Files List -->
<div v-if="state.files.length" class="text-left mt-4">
    <div class="flex justify-between items-center mb-2">
        <h3 class="font-semibold">Selected Files:</h3>
        <span class="text-xs text-gray-500">
            {{ state.files.length }}/{{ MAX_FILES }}
            ({{ (totalFileSize / (1024 * 1024)).toFixed(2) }}MB)
        </span>
    </div>
    <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto">
        <div v-for="fileObj in state.files" :key="fileObj.file.name"
            class="flex items-center bg-gray-100 p-2 rounded">
            <!-- File Preview -->
            <div class="mr-3 flex-shrink-0">
                <img v-if="fileObj.preview" :src="fileObj.preview" 
                    class="w-12 h-12 object-cover rounded" />
                <ImageIcon v-else class="w-12 h-12 text-gray-400" />
            </div>

            <div class="flex-grow overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-sm truncate pr-2">
                        {{ fileObj.file.name }}
                    </span>
                    <button @click="removeFile(fileObj)" 
                        class="text-red-500 hover:text-red-700 ml-2"
                        aria-label="Remove file">
                        <Trash2Icon class="w-4 h-4" />
                    </button>
                </div>
                
                <!-- Upload Progress -->
                <div v-if="uploading" class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                    <div class="bg-blue-600 h-1.5 rounded-full" 
                        :style="{width: `${state.uploadProgress[fileObj.file.name] || 0}%`}">
                    </div>
                </div>

                <div class="text-xs text-gray-500 flex justify-between mt-0.5">
                    <span class="truncate pr-2">
                        {{ ALLOWED_TYPES[fileObj.file.type] || 'Unknown' }}
                    </span>
                    <span class="flex-shrink-0">
                        {{ (fileObj.file.size / 1024).toFixed(1) }}KB
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Overall Upload Progress -->
                <div v-if="uploading" class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" 
                            :style="{width: `${overallUploadProgress}%`}">
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Overall Progress: {{ Math.round(overallUploadProgress) }}%
                    </p>
                </div>
            </div>

            <!-- Upload Information -->
            <div class="text-xs text-gray-500 mt-2">
                <p>Supported file types: {{ Object.values(ALLOWED_TYPES).join(', ') }}</p>
                <p>Maximum file size: 50MB per file</p>
                <p>Maximum total files: {{ MAX_FILES }}</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end mt-4 space-x-2">
                <button @click="$emit('close')" 
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </button>
                <button @click="handleUpload" 
                    :disabled="!state.files.length || uploading"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 transition">
                    {{ uploading ? `Uploading (${Math.round(overallUploadProgress)}%)` : "Upload" }}
                </button>
            </div>
        </div>
    </div>
</template>