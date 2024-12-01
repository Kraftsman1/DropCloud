<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProviderForm from '@/Components/StorageProviders/ProviderForm.vue'

const createProvider = (formData) => {
    axios.post(route('storage-providers.store'), formData);

}

</script>
<template>
    <AppLayout>
        <main class="flex-1 p-8 overflow-auto">

            <header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create Storage Provider
                </h2>
            </header>

            <div class="py-12">
                <div class="mx-auto">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <ProviderForm @submitted="createProvider" submit-button-text="Create Provider" />
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>