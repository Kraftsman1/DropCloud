<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationMark from "@/Components/ApplicationMark.vue";
import { CloudIcon, FolderIcon, ShareIcon, ShieldCheckIcon } from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const appName = import.meta.env.VITE_APP_NAME || 'Dropcloud';

const features = [
    {
        icon: CloudIcon,
        title: 'Centralized File Management',
        description: 'Store, access and manage all your files from a single dashboard, anywhere, anytime.'
    },
    {
        icon: FolderIcon,
        title: 'Seamless Integration',
        description: 'Connect multiple storage providers effortlessly.'
    },
    {
        icon: ShareIcon,
        title: 'Easy Sharing',
        description: "Share files and folders with anyone, across multiple teams."
    },
    {
        icon: ShieldCheckIcon,
        title: 'Advanced File Management',
        description: 'Create folders, tag files, and customize your workspace.'
    }
];
</script>

<template>

    <Head title="Store, share, collaborate and more" />

    <div
        class="relative min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <!-- Navigation -->
        <header class="bg-white shadow-md">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <a href="#" class="flex items-center space-x-2">
                            <ApplicationMark class="block h-9 w-auto" />
                            <span class="text-xl font-bold">{{ appName }}</span>
                        </a>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <nav class="hidden md:flex items-center space-x-4">
                            <a href="#" class="text-gray-500 hover:text-gray-700">
                                Features
                            </a>
                            <a href="#" class="text-gray-500 hover:text-gray-700">
                                Pricing
                            </a>
                            <a href="#" class="text-gray-500 hover:text-gray-700">
                                Support
                            </a>
                        </nav>
                        <div v-if="canLogin" class="flex items-center space-x-4">
                            <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                                class="inline-flex items-center gap-2 rounded border border-blue-600 bg-blue-600 px-4 py-2 text-white hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500">
                            Dashboard
                            </Link>

                            <template v-else>
                                <Link :href="route('login')"
                                    class="inline-flex items-center gap-2 rounded border border-blue-600 px-4 py-2 text-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring active:bg-blue-500">
                                Log in
                                </Link>

                                <Link v-if="canRegister" :href="route('register')"
                                    class="inline-flex items-center gap-2 rounded border border-blue-600 bg-blue-600 px-4 py-2 text-white hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500">
                                Register
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>



        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto min-h-full sm:px-6 lg:px-8 pt-20 pb-16">
            <main class="container mx-auto px-6 py-16 flex flex-col md:flex-row items-center justify-between">
                <!-- Left Content -->
                <div class="max-w-xl mb-12 md:mb-0">
                    <h1 class="text-5xl font-bold leading-tight mb-6">
                        All your files, anywhere and everywhere
                    </h1>
                    <p class="text-gray-400 text-lg mb-8">
                        Store, share, and collaborate on files and folders from any mobile device, tablet, or computer
                    </p>
                    <div class="flex items-center space-x-4">
                        <Link :href="route('register')"
                            class="inline-flex items-center gap-2 rounded border border-blue-600 bg-blue-600 px-4 py-2 text-white hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500">
                            Get Started
                    </Link>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-start space-x-12 mt-16">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-2xl font-bold">1M+</span>
                            </div>
                            <p class="text-gray-400">active users</p>
                        </div>

                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span class="text-2xl font-bold">5TB+</span>
                            </div>
                            <p class="text-gray-400">files stored</p>
                        </div>

                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-2xl font-bold">6M+</span>
                            </div>
                            <p class="text-gray-400">uploaded files</p>
                        </div>
                    </div>
                </div>

                <!-- Right Illustration -->
                <div class="w-full md:w-1/2 flex justify-center">
                    <div class="relative w-80 h-80">
                        <div class="absolute inset-0 bg-blue-600 rounded-lg transform rotate-6"></div>
                        <div class="absolute inset-0 bg-white/60 rounded-lg transform rotate-3"></div>
                        <div class="absolute inset-0 bg-blue-600 rounded-lg"></div>
                    </div>
                </div>
            </main>
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
                        <div v-for="feature in features" :key="feature.title" class="flex flex-col items-start">
                            <div
                                class="rounded-lg bg-gray-100 dark:bg-gray-700 p-2 ring-1 ring-gray-900/10 dark:ring-gray-100/10">
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
                            "We've always been a pioneer in the card industry, and we think of ourselves in the same way
                            in the
                            digital space. Thankfully, we pretty much have three words internally that keep us creating
                            great
                            work: 'It's on Dropbox.'"
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
                        <li>
                            <Link href="#" class="hover:text-gray-300">Desktop app</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Mobile app</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Features</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Security</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold mb-4">Products</h3>
                    <ul class="space-y-2">
                        <li>
                            <Link href="#" class="hover:text-gray-300">Plus</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Professional</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Business</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Enterprise</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li>
                            <Link href="#" class="hover:text-gray-300">Help center</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Contact us</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Privacy & terms</Link>
                        </li>
                        <li>
                            <Link href="#" class="hover:text-gray-300">Cookie policy</Link>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</template>