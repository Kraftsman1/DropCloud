<!-- Components/ActionMessage.vue -->
<script setup>
import { CheckCircle, XCircle, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    message: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info'].includes(value)
    },
    autoClose: {
        type: Boolean,
        default: true
    },
    duration: {
        type: Number,
        default: 5000
    }
});

const emit = defineEmits(['close']);

// Auto-close functionality
if (props.autoClose) {
    setTimeout(() => {
        emit('close');
    }, props.duration);
}

// Computed properties for styling
const styles = {
    success: {
        bg: 'bg-green-50',
        border: 'border-green-400',
        text: 'text-green-800',
        icon: CheckCircle,
        iconColor: 'text-green-400'
    },
    error: {
        bg: 'bg-red-50',
        border: 'border-red-400',
        text: 'text-red-800',
        icon: XCircle,
        iconColor: 'text-red-400'
    },
    info: {
        bg: 'bg-blue-50',
        border: 'border-blue-400',
        text: 'text-blue-800',
        icon: AlertCircle,
        iconColor: 'text-blue-400'
    }
};
</script>

<template>
    <div
        v-if="message"
        :class="[
            styles[type].bg,
            styles[type].text,
            'border-l-4',
            styles[type].border,
            'p-4 mb-4 rounded-r shadow-sm'
        ]"
        role="alert"
    >
        <div class="flex items-center">
            <component
                :is="styles[type].icon"
                :class="[styles[type].iconColor, 'w-5 h-5 mr-2']"
            />
            <span class="flex-1">{{ message }}</span>
            <button
                @click="$emit('close')"
                class="ml-auto hover:opacity-75 transition-opacity"
                aria-label="Close"
            >
                <XCircle class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>