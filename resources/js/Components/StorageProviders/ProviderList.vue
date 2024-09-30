<template>
    <div>
        <div v-for="provider in providers" :key="provider.id" class="mb-4">
            <ProviderListItem :provider="provider" @delete="$emit('delete', provider.id)" />
        </div>
    </div>
</template>

<script>
import ProviderListItem from './ProviderListItem.vue'

export default {
    components: {
        ProviderListItem
    },
    props: {
        providers: {
            type: Array,
            required: true
        }
    }
}
</script>