<script setup>
import { computed } from "vue";
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
    selectedItems: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const emit = defineEmits(["navigate", "download", "delete", "select", "deselect"]);

const data = computed(() => props.contents?.data || { folders: [], files: [] });

// Compute all selectable items (files and folders)
const allItems = computed(() => [
    ...data.value.folders.map(folder => ({
        ...folder,
        itemType: 'folder'
    })),
    ...data.value.files.map(file => ({
        ...file,
        itemType: 'file'
    }))
]);

// Check if all items are currently selected
const isAllSelected = computed(() => {
    if (allItems.value.length === 0) return false;
    return allItems.value.every(item => 
        props.selectedItems.some(selected => selected.path === item.path)
    );
});

const isPartiallySelected = computed(() => {
    const selectedCount = allItems.value.filter(item => 
        props.selectedItems.some(selected => selected.path === item.path)
    ).length;
    return selectedCount > 0 && selectedCount < allItems.value.length;
});

const isSelected = (path) => {
    return props.selectedItems.some(item => item.path === path);
};

const handleSelectAll = () => {
    if (isAllSelected.value) {
        // Deselect all items
        emit('deselect', { type: 'all' });
    } else {
        // Select all items
        const itemsToSelect = allItems.value.map(item => ({
            path: item.path,
            type: item.itemType,
            name: extractFileName(item.path)
        }));
        emit('select', { type: 'all', items: itemsToSelect });
    }
};

const extractFileName = (path) => path.split("/").pop();

const formatFileSize = (bytes) => {
    if (!bytes) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`;
};

const formatDate = (timestamp) => {
    return new Date(timestamp * 1000).toLocaleString();
};

const handleItemSelection = (item, isCurrentlySelected) => {
    const itemData = {
        path: item.path,
        type: 'size' in item ? 'file' : 'folder',
        name: extractFileName(item.path)
    };
    emit(isCurrentlySelected ? 'deselect' : 'select', itemData);
};
</script>

<template>
    <div class="bg-white rounded-lg shadow">
        <!-- Table Header -->
        <div class="grid grid-cols-12 gap-4 p-4 font-semibold text-gray-700 border-b">
            <div class="col-span-1 flex items-center justify-center">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        :checked="isAllSelected"
                        :indeterminate="isPartiallySelected"
                        @change="handleSelectAll"
                        :disabled="!allItems.length"
                    />
                    <span class="sr-only">Select All Items</span>
                </label>
            </div>
            <div class="col-span-5">Name</div>
            <div class="col-span-2">Size</div>
            <div class="col-span-2">Modified</div>
            <div class="col-span-2 text-center">Actions</div>
        </div>

        <!-- Directories -->
        <div
            v-for="folder in data.folders"
            :key="folder.path"
            class="grid grid-cols-12 gap-4 p-4 hover:bg-gray-50 border-b group"
            :class="{ 'bg-blue-50': isSelected(folder.path) }"
        >
            <div class="col-span-1 flex items-center justify-center">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    :checked="isSelected(folder.path)"
                    @change="handleItemSelection(folder, isSelected(folder.path))"
                />
            </div>
            <div class="col-span-5 flex items-center space-x-2 cursor-pointer" @click="emit('navigate', folder.path)">
                <FolderIcon class="w-5 h-5 flex-shrink-0 text-yellow-500" />
                <span class="truncate">{{ extractFileName(folder.path) }}</span>
            </div>
            <div class="col-span-2 flex items-center text-gray-500">--</div>
            <div class="col-span-2 flex items-center text-gray-500">--</div>
            <div class="col-span-2 flex items-center justify-center space-x-2">
                <button
                    @click.stop="emit('delete', { path: folder.path, type: 'folder', name: extractFileName(folder.path) })"
                    class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded hover:bg-red-50"
                    title="Delete Folder"
                >
                    <TrashIcon class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Files -->
        <div
            v-for="file in data.files"
            :key="file.path"
            class="grid grid-cols-12 gap-4 p-4 hover:bg-gray-50 border-b group"
            :class="{ 'bg-blue-50': isSelected(file.path) }"
        >
            <div class="col-span-1 flex items-center justify-center">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    :checked="isSelected(file.path)"
                    @change="handleItemSelection(file, isSelected(file.path))"
                />
            </div>
            <div class="col-span-5 flex items-center space-x-2">
                <FileIcon class="w-5 h-5 flex-shrink-0 text-blue-500" />
                <span class="truncate">{{ extractFileName(file.path) }}</span>
            </div>
            <div class="col-span-2 flex items-center">
                {{ formatFileSize(file.size) }}
            </div>
            <div class="col-span-2 flex items-center">
                {{ formatDate(file.last_modified) }}
            </div>
            <div class="col-span-2 flex items-center justify-center space-x-2">
                <button
                    @click="emit('download', file.path)"
                    class="text-blue-500 hover:text-blue-700 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded hover:bg-blue-50"
                    title="Download File"
                >
                    <DownloadIcon class="w-5 h-5" />
                </button>
                <button
                    @click="emit('delete', { path: file.path, type: 'file', name: extractFileName(file.path) })"
                    class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded hover:bg-red-50"
                    title="Delete File"
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