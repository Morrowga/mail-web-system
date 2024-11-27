<script setup>
import { onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import FilterDialog from '@/PageComponents/FilterDialog.vue';
import DropDownMenu from '@/Components/DropDownMenu.vue';
import { useI18n } from 'vue-i18n';
import { permissionGrant } from '@/Helper/permissionUtils';

const { props } = usePage();

const { t, locale } = useI18n();

const permissions = props?.auth?.user?.permissions
const role = props?.auth?.user?.role

const defaultAccDowns = [
    { label: t('nav.profile'), href: 'profile.edit', post: false, show: true },
    { label: t('nav.logout'), href: 'logout', post: true, show: true },
];

const AccDowns = [
    { label: t('nav.users'), href: 'users.index', post: false, show: permissionGrant(permissions, 'account_read') },
    { label: t('nav.roles'), href: 'roles.index', post: false, show: role == '管理者' ? true : false },
    { label: t('nav.permissions'), href: 'permissions.index', post: false, show: role == '管理者' ? true : false },
];
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
                        <div class="hidden sm:ms-6 sm:flex sm:items-center" style="width: 50%;">
                            <h2 style="color: #fff;">{{ $t('nav.logo') }}</h2>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center justify-end" style="width: 92%;">
                            <NavLink
                                v-if="permissionGrant(permissions, 'mail_read')"
                                :active="route().current('inbox')"
                                :href="route('inbox')"
                                as="button"
                                class="mx-5 layout-nav-text"
                            >
                                {{ $t('nav.inbox') }}
                            </NavLink>
                            <DropDownMenu :title="$t('nav.account')" v-if="permissionGrant(permissions, 'account_read')" :content="AccDowns" />
                            <DropDownMenu :title="$page.props.auth.user.name" :content="defaultAccDowns" />
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
                                :href="route('inbox')"
                            >
                                {{ $t('nav.inbox') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                class="layout-nav-text"
                                :href="route('users.index')"
                            >
                                {{ $t('nav.users') }}
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

            <FilterDialog
                :filterDialog="filterDialogVisible"
                :form="form"
                @handleSubmit="handleSubmit"
                @update:filterDialog="filterDialogVisible = $event"
            />
        </div>
    </div>
</template>

<style>
.layout-nav-text{
    color: #fff !important;
    text-decoration: none !important;
}
</style>
