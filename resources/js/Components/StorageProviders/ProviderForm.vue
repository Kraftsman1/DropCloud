<template>
    <form @submit.prevent="submit">
        <div>
            <label for="label" class="block font-medium text-sm text-gray-700">Label</label>
            <input id="label" v-model="form.label" type="text" class="mt-1 block w-full" required autofocus />
        </div>

        <div class="mt-4">
            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
            <input id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
        </div>

        <div class="mt-4">
            <label for="driver" class="block font-medium text-sm text-gray-700">Driver</label>
            <select id="driver" v-model="form.configuration.driver" class="mt-1 block w-full" required>
                <option value="local">Local</option>
                <option value="s3">Amazon S3</option>
                <option value="google">Google Cloud Storage</option>
            </select>
        </div>

        <!-- Conditional fields based on selected driver -->
        <div v-if="form.configuration.driver === 's3'" class="mt-4">
            <label for="s3_key" class="block font-medium text-sm text-gray-700">S3 Key</label>
            <input id="s3_key" v-model="form.configuration.key" type="text" class="mt-1 block w-full" required />

            <label for="s3_secret" class="block font-medium text-sm text-gray-700 mt-4">S3 Secret</label>
            <input id="s3_secret" v-model="form.configuration.secret" type="password" class="mt-1 block w-full"
                required />

            <label for="s3_region" class="block font-medium text-sm text-gray-700 mt-4">S3 Region</label>
            <input id="s3_region" v-model="form.configuration.region" type="text" class="mt-1 block w-full" required />

            <label for="s3_bucket" class="block font-medium text-sm text-gray-700 mt-4">S3 Bucket</label>
            <input id="s3_bucket" v-model="form.configuration.bucket" type="text" class="mt-1 block w-full" required />
        </div>

        <!-- Add similar sections for other drivers -->

        <div class="flex items-center justify-end mt-4">
            <!-- Test connection button -->

            <button
                class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                @click.prevent="testConnection" :disabled="isTestingConnection">
                {{ isTestingConnection ? "Testing..." : "Test Connection" }}
            </button>

            <button type="submit"
                class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                {{ submitButtonText }}
            </button>
        </div>
    </form>
    <!-- Connection Test Response -->
    <div v-if="testResponse" class="mt-4">
        <p :class="testResponse.success ? 'text-green-600' : 'text-red-600'">
            {{ testResponse.message }}
        </p>
    </div>
</template>

<script>
export default {
    props: {
        provider: {
            type: Object,
            default: () => ({
                label: "",
                name: "",
                configuration: {
                    driver: "local",
                },
            }),
        },
        submitButtonText: {
            type: String,
            default: "Save",
        },
    },
    data() {
        return {
            form: this.provider,
            testResponse: null,
            isTestingConnection: false,
        };
    },
    methods: {
        submit() {
            this.$emit("submitted", this.form);
        },
        async testConnection() {
            this.testResponse = null;
            console.log(this.form.configuration);
            try {
                this.isTestingConnection = true;
                const response = await axios.post(
                    `/storage-providers/test-connection`,
                    this.form.configuration
                );
                this.testResponse = response.data;
            } catch (error) {
                this.testResponse = {
                    success: false,
                    message: error.response.data.message,
                };
            } finally {
                this.isTestingConnection = false;
            }
        },
    },
};
</script>
