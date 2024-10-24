<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CloudIcon, FolderIcon, ShareIcon, ShieldCheckIcon } from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const features = [
    {
        icon: CloudIcon,
        title: 'Cloud Storage',
        description: 'Store and access your files from anywhere, anytime.'
    },
    {
        icon: FolderIcon,
        title: 'File Organization',
        description: 'Keep your files organized with folders and smart categorization.'
    },
    {
        icon: ShareIcon,
        title: 'Easy Sharing',
        description: "Share files and folders with anyone, even if they don't have an account."
    },
    {
        icon: ShieldCheckIcon,
        title: 'Secure Storage',
        description: 'Your files are encrypted and stored securely in the cloud.'
    }
];
</script>

<template>
    <Head title="Store, share, collaborate and more" />

    <div class="relative min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <!-- Navigation -->
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:end-0 p-6 text-end z-10">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                  class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" 
                      class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" 
                      class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Register
                </Link>
            </template>
        </div>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
                    Your files, anywhere and everywhere
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Store, share, and collaborate on files and folders from any mobile device, tablet, or computer
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <Link v-if="canRegister" :href="route('register')"
                          class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        Get started
                    </Link>
                    <Link :href="route('login')"
                          class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300">
                        Sign in <span aria-hidden="true">→</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="bg-white dark:bg-gray-800 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-blue-600">Everything you need</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        All-in-one platform for your files
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-4">
                        <div v-for="feature in features" :key="feature.title" 
                             class="flex flex-col items-start">
                            <div class="rounded-lg bg-gray-100 dark:bg-gray-700 p-2 ring-1 ring-gray-900/10 dark:ring-gray-100/10">
                                <component :is="feature.icon" class="h-6 w-6 text-blue-600" />
                            </div>
                            <dt class="mt-4 font-semibold text-gray-900 dark:text-white">
                                {{ feature.title }}
                            </dt>
                            <dd class="mt-2 leading-7 text-gray-600 dark:text-gray-300">
                                {{ feature.description }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
                <!-- Testimonial Section -->
        <section class="px-6 py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold mb-12">What our customers are saying</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-orange-500 p-12 rounded-lg flex items-center justify-center">
                        <img src="/topps-logo.svg" alt="Topps" class="h-16" />
                    </div>
                    <div class="flex flex-col justify-center">
                        <blockquote class="text-xl mb-4">
                            "We've always been a pioneer in the card industry, and we think of ourselves in the same way in the digital space. Thankfully, we pretty much have three words internally that keep us creating great work: 'It's on Dropbox.'"
                        </blockquote>
                        <cite class="text-gray-600">
                            John Doe, VP - Engineering @Dropbox
                        </cite>
                        
                    </div>
                </div>
            </div>
        </section>

                <!-- Footer -->
        <footer class="bg-black text-white px-6 py-16">
            <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold mb-4">DropCloud</h3>
                    <ul class="space-y-2">
                        <li><Link href="#" class="hover:text-gray-300">Desktop app</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Mobile app</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Features</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Security</Link></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold mb-4">Products</h3>
                    <ul class="space-y-2">
                        <li><Link href="#" class="hover:text-gray-300">Plus</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Professional</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Business</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Enterprise</Link></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><Link href="#" class="hover:text-gray-300">Help center</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Contact us</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Privacy & terms</Link></li>
                        <li><Link href="#" class="hover:text-gray-300">Cookie policy</Link></li>
                    </ul>
                </div>
            </div>
        </footer>  
    </div>
</template>