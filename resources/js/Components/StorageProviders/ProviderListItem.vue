<template>
    <div class="max-w-sm rounded border-2 border-dashed border-black overflow-hidden shadow-lg bg-white">
        <div class="px-6 py-4">
            <h3 class="text-xl font-bold mb-2">{{ provider.label }}</h3>
            <p class="text-gray-700 text-base">{{ provider.name }}</p>
            <p class="text-gray-600 text-sm">Driver: {{ provider.configuration.driver }}</p>
        </div>
        <div class="px-6 pt-4 pb-2 flex justify-between">
            <inertia-link :href="route('storage-providers.edit', provider.id)" 
                class="text-indigo-600 hover:text-indigo-900">Edit</inertia-link>
            <button @click="$emit('delete')" class="text-red-600 hover:text-red-900">Delete</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        provider: {
            type: Object,
            required: true
        }
    }
}
</script>