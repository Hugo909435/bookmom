<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-slate-50">
            <nav class="bg-white border-b border-slate-200 shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('books.index')" class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <span class="font-bold text-blue-700 text-lg hidden sm:block">BookMom</span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">
                                <NavLink :href="route('books.index')" :active="route().current('books.*')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                    :class="route().current('books.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100'"
                                >
                                    Livres
                                </NavLink>
                                <NavLink :href="route('movies.index')" :active="route().current('movies.*')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                    :class="route().current('movies.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100'"
                                >
                                    Films
                                </NavLink>
                                <NavLink :href="route('stats.index')" :active="route().current('stats.*')"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                    :class="route().current('stats.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100'"
                                >
                                    Statistiques
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 hover:text-blue-600 hover:bg-slate-100 transition-colors">
                                        <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-700 font-semibold text-xs">{{ $page.props.auth.user.name.charAt(0).toUpperCase() }}</span>
                                        </div>
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profil</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Déconnexion</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-t border-slate-200">
                    <div class="space-y-1 px-4 py-3">
                        <ResponsiveNavLink :href="route('books.index')" :active="route().current('books.*')">Livres</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('movies.index')" :active="route().current('movies.*')">Films</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('stats.index')" :active="route().current('stats.*')">Statistiques</ResponsiveNavLink>
                    </div>
                    <div class="border-t border-slate-200 px-4 py-3">
                        <div class="text-sm font-medium text-slate-800">{{ $page.props.auth.user.name }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $page.props.auth.user.email }}</div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profil</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Déconnexion</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white border-b border-slate-200">
                <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
