<script setup>
import { onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const { props } = usePage();


</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-[#4891dc] fixed top-0 left-0 w-full z-10"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="hidden sm:ms-6 sm:flex sm:items-center" style="width: 8%;">
                            <h5 style="color: #fff;">メールボックス</h5>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center justify-center" style="width: 92%;">
                            <!-- Settings Dropdown -->
                            <div class="hidden sm:ms-6 sm:flex sm:items-center" style="width: 55%;">
                                <VTextField
                                    :loading="loading"
                                    prepend-inner-icon="mdi-magnify"
                                    density="compact"
                                    variant="solo"
                                    hide-details
                                    single-line
                                    @click:append-inner="onClick"
                                ></VTextField>
                            </div>

                            <NavLink
                                class="mx-5 layout-nav-text d-flex align-center"
                                :active="route().current('dashboard')"
                                :href="route('dashboard')"
                                as="button"
                            >
                                <span>{{ $t('nav.inbox') }}</span>
                            </NavLink>
                            <NavLink
                                :active="route().current('templinates')"
                                :href="route('templates.index')"
                                as="button"
                                class="mx-5 layout-nav-text"
                            >
                                {{ $t('nav.template') }}
                            </NavLink>
                            <NavLink
                                :active="route().current('folders')"
                                as="button"
                                :href="route('folders.index')"
                                class="mx-5 layout-nav-text"
                            >
                                {{ $t('nav.folders') }}
                            </NavLink>
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md px-3 pt-2 text-sm font-medium leading-4 layout-nav-text transition duration-150 ease-in-out
                                                text-capitalize"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                         {{ $t('nav.profile') }}
                                        </DropdownLink>
                                        <DropdownLink
                                            @click="router.post(route('logout'))"
                                            as="button"
                                        >
                                        {{ $t('nav.logout') }}
                                    </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1"
                    >
                    <div class="space-y-1">
                            <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('dashboard')"
                            >
                                {{ $t('nav.inbox') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('templates.index')"
                            >
                                {{ $t('nav.template') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('folders.index')"
                            >
                                {{ $t('nav.folders') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('profile.edit')"
                            >
                                {{ $t('nav.profile') }}
                            </ResponsiveNavLink>
                            <!-- <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('dashboard')"
                                as="button"
                            >
                            </ResponsiveNavLink> -->
                        </div>

                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 layout-nav-text"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500 layout-nav-text">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <!-- <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink> -->
                            <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main style="padding-top: 8vh; padding-left: 2vh; padding-right: 2vh;">
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
.layout-nav-text{
    color: #fff !important;
    text-decoration: none !important;
}
</style>
