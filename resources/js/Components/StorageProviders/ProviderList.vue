<script setup>
import { ref } from 'vue';
import ProviderListItem from './ProviderListItem.vue';
import ConfirmationModal from '../ConfirmationModal.vue';

const props = defineProps({
    providers: {
        type: Array,
        required: true
    }
});

console.log(props.providers)

const showModal = ref(false);

const confirmDelete = () => {
    showModal.value = false;
    emit('delete');
}

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="provider in providers" :key="provider.id" class="mb-4">
            <ProviderListItem :provider="provider" @delete="showModal = true" />
            <ConfirmationModal :show="showModal" @close="showModal = false">
                <template #title>
                    Confirm Delete
                </template>
                <template #content>
                    Are you sure you want to delete this Storage Provider?
                </template>
                <template #footer>
                    <button @click="confirmDelete" class="bg-red-600 text-white px-4 py-2 rounded mr-2">Delete</button>
                    <button @click="showModal = false" class="bg-gray-300 text-black px-4 py-2 rounded">Cancel</button>
                </template>
            </ConfirmationModal>
        </div>
    </div>
</template>