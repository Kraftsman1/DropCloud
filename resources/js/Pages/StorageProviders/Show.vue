<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Storage Provider Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ provider.label }}</h3>
                    <p><strong>Name:</strong> {{ provider.name }}</p>
                    <p><strong>Driver:</strong> {{ provider.configuration.driver }}</p>

                    <div v-if="provider.configuration.driver === 's3'" class="mt-4">
                        <h4 class="font-semibold">S3 Configuration</h4>
                        <p><strong>Region:</strong> {{ provider.configuration.region }}</p>
                        <p><strong>Bucket:</strong> {{ provider.configuration.bucket }}</p>
                    </div>

                    <!-- Add similar sections for other driver types -->

                    <div class="mt-4">
                        <inertia-link :href="route('storage-providers.edit', provider.id)"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Edit Provider
                        </inertia-link>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
    components: {
        AppLayout
    },
    props: {
        provider: Object
    }
}
</script>