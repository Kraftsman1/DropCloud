<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProviderForm from '@/Components/StorageProviders/ProviderForm.vue'

const createProvider = (formData) => {
    $inertia.post(route('storage-providers.store'), formData)
}

</script>
<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Storage Provider
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <ProviderForm @submitted="createProvider" submit-button-text="Create Provider" />
                </div>
            </div>
        </div>
    </app-layout>
</template>