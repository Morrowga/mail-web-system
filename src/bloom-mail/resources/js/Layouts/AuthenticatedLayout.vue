<script setup>
import { onMounted, ref, computed } from 'vue';
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
import DropDownSystem from '@/Components/DropDownSystem.vue';

const { props } = usePage();
const drawer = ref(true)
const rail = ref(false)
const { t, locale } = useI18n();

const permissions = props?.auth?.user?.permissions
const role = props?.auth?.user?.role

const defaultAccDowns = [
    { label: t('nav.profile'), href: 'profile.edit', post: false, show: true },
    { label: t('nav.logout'), href: 'logout', post: true, show: true },
];

const merchandiseArray = [
    { label: t('system.nav.product_list'), href: 'products.index', post: false, show: permissionGrant(permissions, 'product_read') },
    { label: t('system.nav.new_product'), href: 'products.create', post: false, show: permissionGrant(permissions, 'product_createdit') },
];

const notificationArray = [
    { label: t('system.nav.notification_list'), href: 'notifications.index', post: false, show: permissionGrant(permissions, 'noti_read') },
    { label: t('system.nav.app_notification'), href: 'notifications.create', post: false, show: permissionGrant(permissions, 'noti_createdit') },
];


const memberArray = [
    { label: t('system.nav.member_list'), href: 'members.index', post: false, show: permissionGrant(permissions, 'account_read') },
    { label: t('system.nav.new_member'), href: 'members.create', post: false, show: permissionGrant(permissions, 'account_read') },
];

const AccDowns = [
    { label: t('nav.users'), href: 'users.index', post: false, show: permissionGrant(permissions, 'account_read') },
    { label: t('nav.roles'), href: 'roles.index', post: false, show: role == '管理者' ? true : false },
    { label: t('nav.permissions'), href: 'permissions.index', post: false, show: role == '管理者' ? true : false },
];

// Compute main content margin based on drawer state
const contentStyle = computed(() => ({
    marginLeft: rail.value ? '0px' : '0px', // Adjust these values based on your drawer width
    transition: 'margin-left 0.2s'
}));

</script>

<template>
    <div>
        <div>
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />

                </div>
            </header>

            <v-card>
                <v-layout>
                    <v-navigation-drawer
                        v-model="drawer"
                        :rail="rail"
                        permanent
                        @click="rail = false"
                    >
                        <v-list-item
                            nav
                        >
                            <template v-slot:append>
                                <div class="text-left d-flex justify-start my-3">
                                    <img src="/images/bloomlogo.png" width="100%" alt="">
                                </div>
                                <v-btn
                                    icon="mdi-chevron-left"
                                    variant="text"
                                    @click.stop="rail = !rail"
                                ></v-btn>
                            </template>
                        </v-list-item>

                        <!-- <v-divider></v-divider> -->

                        <v-list density="compact" nav>
                            <v-list-item @click="router.get('/home')" :prepend-icon="rail ? 'mdi-home-variant-outline': ''" :class="route().current('dashboard') ? 'border-blue' : ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.top')"
                                    icon="mdi-home-variant-outline"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item @click="router.get(route('inbox'))" :prepend-icon="rail ? 'mdi-email-outline': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.email')"
                                    icon="mdi-email-outline"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-account-group': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.membership')"
                                    icon="mdi-account-group"
                                    :content="memberArray"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-alpha-p-circle': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.points')"
                                    icon="mdi-alpha-p-circle"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-credit-card': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.payment')"
                                    icon="mdi-credit-card"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-cube-outline': ''" :class="route().current('products.index') ? 'border-blue' : ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.product')"
                                    icon="mdi-cube-outline"
                                    :content="merchandiseArray"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-alert-circle': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.notification')"
                                    icon="mdi-alert-circle"
                                    :content="notificationArray"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-bell-outline': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.app_notification')"
                                    icon="mdi-bell-outline"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-ticket-outline': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.coupon')"
                                    icon="mdi-ticket-outline"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-finance': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.entry')"
                                    icon="mdi-finance"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-image-size-select-actual': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.banner')"
                                    icon="mdi-image-size-select-actual"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-map-marker': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.store')"
                                    icon="mdi-map-marker"
                                    :content="[]"
                                />
                            </v-list-item>
                            <v-list-item :prepend-icon="rail ? 'mdi-message-reply-text-outline': ''">
                                <div v-if="rail" @click="rail = false"></div>
                                <DropDownSystem
                                    v-else
                                    :title="$t('system.nav.chat')"
                                    icon="mdi-message-reply-text-outline"
                                    :content="[]"
                                />
                            </v-list-item>
                        </v-list>
                    </v-navigation-drawer>

                    <v-main>
                        <nav
                            class="border-b border-gray-100 bg-[#f0f2f6] fixed top-0 left-0 w-full z-10"
                        >
                            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                <div class="flex h-16 justify-between">
                                    <div class="hidden sm:ms-6 sm:flex sm:items-center justify-end" style="width: 100%;">
                                        <!-- <NavLink
                                            v-if="permissionGrant(permissions, 'mail_read')"
                                            :active="route().current('inbox')"
                                            :href="route('inbox')"
                                            as="button"
                                            class="mx-5 layout-nav-text-admin"
                                        >
                                            <VIcon icon="mdi-email-outline" class="mx-3"></VIcon>
                                            {{ $t('nav.inbox') }}
                                        </NavLink> -->
                                        <DropDownMenu :title="$t('nav.account')" v-if="permissionGrant(permissions, 'account_read')" icon="mdi-cog-outline" :content="AccDowns" />
                                        <DropDownMenu :title="$page.props.auth.user.name" :content="defaultAccDowns" icon="mdi-account" />
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
                        </nav>
                        <div class="bg-[#f2f4f6] min-h-screen" :style="contentStyle">
                            <div class="mx-auto sm:px-6 lg:px-5 pt-16">
                                <div
                                    class="overflow-hidden sm:rounded-lg"
                                >
                                    <slot />
                                </div>
                            </div>
                        </div>
                    </v-main>
                </v-layout>
            </v-card>
        </div>
    </div>
</template>

<style scoped>
.v-navigation-drawer {
    position: fixed !important;
    z-index: 100;
}

.v-main {
    min-height: 100vh;
    width: 100%;
}

/* Ensure top nav bar doesn't overlap with content */
nav {
    transition: padding-left 0.2s;
}

.layout-nav-text-admin{
    color: #000 !important;
    text-decoration: none !important;
}

.border-blue {
    border-right: 7px solid #41aecc;
    border-radius: 0px;
}
</style>
