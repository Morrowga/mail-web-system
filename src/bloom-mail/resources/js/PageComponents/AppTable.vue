<script setup>
import { getStatusColor, getTranslatedStatus } from '@/Helper/status';
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';
import AppConfirmDialog from './AppConfirmDialog.vue';
import PaginationServerSide from './PaginationServerSide.vue';
import usePagination from '@/Helper/usePagination';
import { permissionGrant } from '@/Helper/permissionUtils';
import moment from 'moment';

moment.locale('ja');

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  headers: {
    type: Array,
    required: true
  },
  url: {
    type: String
  },
  permission_name: {
    type: String
  },
  tableTitle: {
    type: String
  }
});

const perPage = ref('10');
const queryParams = ref({});

const pagination = ref({
    current_page: props?.data?.current_page,
    per_page: props?.data.per_page,
    total: props?.data.total
});

const updatePerPage = () => {
  const currentUrl = props?.url;
  const newUrl = addOrUpdateQueryParam(currentUrl, 'per_page', perPage.value);
  router.get(newUrl);
};

const addOrUpdateQueryParam = (url, param, value) => {
  const urlObj = new URL(url, window.location.origin);
  urlObj.searchParams.set(param, value);  // Set or update the query parameter
  return urlObj.href;  // Return the updated URL
};

const page = usePage();

const permissions = page?.props?.auth?.user?.permissions

const { t, locale } = useI18n();

const emit = defineEmits();

const getEdit = (id) => {
    router.get(props?.url + '/' + id + '/edit')
}

const fetchData = (page) => {
  router.get(props.url, { page: page });
};

watch(() => pagination.value.current_page, (newPage) => {
  fetchData(newPage);
});

console.log(permissionGrant(permissions, props?.permission_name + '_delete'));

const paginate = usePagination(props.data);

// Add this function to handle column widths
const getColumnWidth = (header) => {
  const columnWidths = {
    'shop_name': { width: '200px', minWidth: '200px' },
    'treatment_begin_date': { width: '150px', minWidth: '150px' },
    'product_detail': { width: '300px', minWidth: '300px' },
    'price': { width: '120px', minWidth: '120px' },
    'sale_start_date': { width: '150px', minWidth: '150px' },
    'sale_end_date': { width: '150px', minWidth: '150px' },
    'status': { width: '100px', minWidth: '100px' },
    'description': { width: '300px', minWidth: '300px' },
    'name': { width: '200px', minWidth: '200px' },
    'login_id': { width: '200px', minWidth: '200px' },
    'display': { width: '200px', minWidth: '200px' },
    'description': { width: '200px', minWidth: '200px' },
    'role_name': { width: '200px', minWidth: '200px' },
  };

  return columnWidths[header.value] || { width: '150px', minWidth: '150px' }; // default width
};

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    queryParams.value = Object.fromEntries(params.entries());
    console.log(queryParams.value.per_page);
    perPage.value = queryParams.value.per_page
});


</script>

<template>
    <VCard class="app-table-card">
        <VCardTitle style="background-color: #F0F2F5;">
            <h4>{{ tableTitle ?? 'Unknown Title' }}</h4>
        </VCardTitle>
        <VCardTitle class="mb-5" style="font-size: 0.8rem; border-bottom: 1px solid rgb(0,0,0,0.1);">
            <div class="d-flex justify-between my-3">
                <p class="pt-3">1−20件 表示／全450件中</p>
                <div class="d-flex ">
                    <div class="d-flex">
                        <span style="padding-top: 0.6rem;">
                            店舗絞り込み：
                        </span>
                        <v-select
                        value="すべて"
                        :items="['すべて', 'すべて', 'すべて', 'すべて', 'すべて', 'すべて']"
                        dense
                        style="width: 120px; font-size: 12px;"
                        variant="outlined" density="compact" required hide-details
                        ></v-select>
                    </div>
                    <div class="d-flex ml-5">
                        <span style="padding-top: 0.6rem;">
                            1ページの表示件数：
                        </span>
                        <v-select
                        :items="['10', '20', '50', '100', '250', '500']"
                        dense
                        style="width: 95px; font-size: 12px;"
                        variant="outlined" density="compact" required hide-details
                        v-model="perPage"
                        @update:modelValue="updatePerPage"
                        ></v-select>
                    </div>

                </div>
            </div>
        </VCardTitle>
        <VCardText style="box-shadow: none;">
            <div class="table-wrapper">
                <VTable class="app-table">
                    <thead class="table-header">
                    <tr>
                        <th class="header-cell" style="min-width: 80px; width: 80px;">
                            商品ID
                        </th>
                        <th
                        class="header-cell"
                        v-for="header in headers"
                        :key="header.value"
                        :style="getColumnWidth(header)"
                        >
                            {{ header.name }}
                        </th>
                        <!-- Fixed action column -->
                        <th
                            class="header-cell fixed-column fixed-header"
                            v-if="url != 'permissions' && permissionGrant(permissions, permission_name + '_delete')"
                            style="min-width: 100px; width: 100px;"
                        >
                        </th>
                    </tr>
                    </thead>

                    <tbody class="tbody-container">
                        <tr v-if="!data?.data?.length">
                            <td :colspan="headers.length" class="text-center pt-5">
                            {{ $t('table.no_data') }}
                            </td>
                        </tr>

                        <tr v-else v-for="(item, index) in data?.data" :key="index" @click="getEdit(item.id)" class="cursor-pointer">
                            <td style="color: #45B4D3; min-width: 80px; width: 80px;">
                                {{ index + 1 }}
                            </td>
                            <td
                            v-for="(header,i) in headers"
                            :key="i"
                            :style="getColumnWidth(header)"
                            >
                                <div v-if="header.value == 'description' || header.value == 'product_detail'">
                                    {{ item[header.value]?.length > 40 ? item[header.value].substring(0, 40) + '...' : item[header.value] }}
                                </div>
                                <div v-else>
                                    {{ item[header.value] }}
                                </div>
                            </td>
                            <!-- Fixed action column -->
                            <td
                            v-if="url != 'permissions' && permissionGrant(permissions, permission_name + '_delete')"
                            class="fixed-column"
                            style="min-width: 100px; width: 100px;"
                            >
                                <VBtn class="mx-3" color="black" style="border-radius: 6px;">{{ $t('system.buttons.edit') }}</VBtn>
                                <AppConfirmDialog :routeUrl="url" :item="item" />
                            </td>
                        </tr>
                    </tbody>
                </VTable>
            </div>
            <div class="my-10">
                <PaginationServerSide
                    :total="paginate.lastPage"
                    :current-page="paginate.currentPage"
                    :next-page-url="paginate.nextPage"
                    :previous-page-url="paginate.previousPage"
                    :base="paginate.path"
                />
            </div>
        </VCardText>
    </VCard>
</template>

<style scoped>
.app-table {
  width: 100%;
  min-height: 60vh;
  border-collapse: separate; /* Changed from collapse */
  border-spacing: 0;
}

.fixed-header {
  background-color: #f0f2f6 !important; /* Match your header background color */
  z-index: 4 !important; /* Higher z-index to stay on top */
}

.table-header {
  background-color: #f0f2f6;
  position: sticky;
  top: 0;
  z-index: 2;
}

.header-cell {
  color: #333;
  font-weight: bold;
  padding: 10px;
  white-space: nowrap; /* Prevent header text wrapping */
}

.tbody-container tr {
  background-color: #fff;
}

.tbody-container tr:nth-child(even) {
  background-color: #f0f2f6;
}

.tbody-container td {
  padding: 10px;
  white-space: nowrap;
}

.app-table-card {
  border-width: 1px;
  border-radius: 15px;
  margin-bottom: 2rem;
}

/* Fixed column styles */
.fixed-column {
  position: sticky !important;
  right: 0;
  background-color: inherit;
  z-index: 3;
  /* box-shadow: -2px 0 5px rgba(0,0,0,0.1); */
}

.fixed-column::after {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  /* background: linear-gradient(to right, rgba(0,0,0,0.1), transparent); */
}

/* Scrollbar styling */
.table-wrapper::-webkit-scrollbar {
  height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.table-wrapper::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
