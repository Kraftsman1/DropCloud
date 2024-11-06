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

                    <ProviderList :providers="providers" @delete="deleteProvider" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProviderList from '@/Components/StorageProviders/ProviderList.vue'

export default {
    components: {
        AppLayout,
        ProviderList
    },
    props: {
        providers: Array
    },
    methods: {
        deleteProvider(id) {
            if (confirm('Are you sure you want to delete this provider?')) {
                this.$inertia.delete(route('storage-providers.destroy', id))
            }
        }
    }
}
</script>