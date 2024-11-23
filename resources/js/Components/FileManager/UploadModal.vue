<script setup>
import { XIcon } from "lucide-vue-next";
import { ref, watch } from "vue";
import { UploadIcon, XIcon } from 'lucide-vue-next';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    onClose: {
        type: Function,
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
    onUpload: {
        type: Function,
        required: true
    },
});

const emit = defineEmits(["close", "upload"]);

const dragOver = ref(false);
const files = ref([]);
const uploading = ref(false);

const handleDrop = (e) => {
    e.preventDefault();
    dragOver.value = false;

    const droppedFiles = e.dataTransfer?.files;
    if (droppedFiles?.length) {
        files.value = droppedFiles;
    }
};

const handleFileSelect = (e) => {
    files.value = e.target.files;
};

const handleUpload = async () => {
    if (!files.value?.length) {
        return;
    }

    uploading.value = true;

    try {
        await emit("upload", files.value, props.currentPath);
        emit("close");
    } catch (error) {
        console.error('Upload failed:', error);
    } finally {
        uploading.value = false;
    }
};

</script>
<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Upload Files</h2>
                <button @click="emit('close')" class="text-gray-500 hover:text-gray-700">
                    <XIcon class="w-6 h-6" />
                </button>
            </div>

            <!-- Upload Area -->
            <div class="border-2 border-dashed rounded-lg p-8 text-center" :class="{
                'border-blue-500 bg-blue-50': dragOver,
                'border-gray-300': !dragOver
            }" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop="handleDrop">
                <UploadIcon class="w-12 h-12 mx-auto text-gray-400 mb-4" />

                <div class="mb-4">
                    <p class="text-gray-600">
                        Drag and drop your files here or
                    </p>
                    <label class="text-blue-500 hover:text-blue-700 cursor-pointer">
                        browse
                        <input type="file" class="hidden" @change="handleFileSelect" multiple>
                    </label>
                </div>

                <div v-if="files?.length" class="text-left mt-4">
                    <h3 class="font-semibold mb-2">Selected Files:</h3>
                    <ul class="space-y-1">
                        <li v-for="file in files" :key="file.name" class="text-sm text-gray-600">
                            {{ file.name }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end mt-4 space-x-2">
                <button @click="emit('close')" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button @click="handleUpload" :disabled="!files?.length || uploading"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50">
                    {{ uploading ? 'Uploading...' : 'Upload' }}
                </button>
            </div>
        </div>
    </div>
</template>