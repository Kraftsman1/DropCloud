<script setup>
import { ref, watch } from "vue";

import AppLayout from "@/Layouts/AppLayout.vue";
import FileContentList from "@/Components/FileManager/FileContentList.vue";

//

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

// log contents\
console.log(props.contents);

const currentPath = ref("/");
const currentContents = ref(props.contents);
const isCreatingFolder = ref(false);
const isUploading = ref(false);
const selectedItems = ref([])

// Watch for path changes
// watch(currentPath, async (newPath) => {
//     try {
//         const response = await axios.get("/file-manager/${props.provider.id}", {
//             params: {
//                 path: newPath,
//             },
//         });
//         if (response.data.success) {
//             currentContents.value = response.data.data;
//             console.log(response.data.data)
//         }
//     } catch (error) {
//         console.error("Failed to fetch contents:", error);
//     }
// });
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

            <div v-if="contents">
                <FileContentList :contents="contents" />
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
