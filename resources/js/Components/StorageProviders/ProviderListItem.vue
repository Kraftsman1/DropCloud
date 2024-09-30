<template>
    <div class="flex items-center justify-between p-4 bg-white shadow rounded-lg">
        <div>
            <h3 class="text-lg font-semibold">{{ provider.label }}</h3>
            <p class="text-sm text-gray-600">{{ provider.name }}</p>
            <p class="text-sm text-gray-600">Driver: {{ provider.configuration.driver }}</p>
        </div>
        <div>
            <inertia-link :href="route('storage-providers.edit', provider.id)"
                class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</inertia-link>
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