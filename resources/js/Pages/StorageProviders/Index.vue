<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProviderList from '@/Components/StorageProviders/ProviderList.vue'

const props = defineProps({
    providers: {
        type: Array,
        required: true
    }
})

const deleteProvider = (id) => {
    if (confirm('Are you sure you want to delete this provider?')) {
        $inertia.delete(route('storage-providers.destroy', id))
    }
}

</script>
<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Storage Providers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <inertia-link :href="route('storage-providers.create')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mb-4">
                        Add New Provider
                    </inertia-link>

                    <p v-if="providers.length === 0" class="text-gray-600">No storage providers found.</p>
                    <ProviderList :providers="providers" @delete="deleteProvider" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
