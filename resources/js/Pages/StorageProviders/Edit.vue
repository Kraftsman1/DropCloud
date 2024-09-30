<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Storage Provider
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <ProviderForm :provider="provider" @submitted="updateProvider"
                        submit-button-text="Update Provider" />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProviderForm from '@/Components/StorageProviders/ProviderForm.vue'

export default {
    components: {
        AppLayout,
        ProviderForm
    },
    props: {
        provider: Object
    },
    methods: {
        updateProvider(formData) {
            this.$inertia.put(route('storage-providers.update', this.provider.id), formData)
        }
    }
}
</script>