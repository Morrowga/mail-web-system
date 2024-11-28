<script setup>
import { getStatusColor, getTranslatedStatus } from '@/Helper/status';
import { ref, watch } from 'vue';
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
  }
});

const pagination = ref({
    current_page: props?.data?.current_page,
    per_page: props?.data.per_page,
    total: props?.data.total
});

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

const paginate = usePagination(props.data);
</script>

<template>
  <VTable class="app-table">
    <thead>
      <tr>
        <th class="header-cell">
            ID
        </th>
        <th class="header-cell" v-for="header in headers" :key="header.value">
          {{ header.name }}
        </th>
        <th class="header-cell" v-if="url != 'permissions' && permissionGrant(permissions, permission_name + '_delete')">

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
        <td>
            {{ index + 1 }}
        </td>
        <td v-for="(header,i) in headers" :key="i">
            {{ item[header.value] }}
        </td>
        <td v-if="url != 'permissions' && permissionGrant(permissions, permission_name + '_delete')">
            <AppConfirmDialog :routeUrl="url" :item="item" />
        </td>
      </tr>
    </tbody>
  </VTable>

  <div class="mt-2">
    <PaginationServerSide
        :total="paginate.lastPage"
        :current-page="paginate.currentPage"
        :next-page-url="paginate.nextPage"
        :previous-page-url="paginate.previousPage"
        :base="paginate.path"
    />
  </div>

</template>

<style scoped>
.app-table {
  width: 100%;
  min-height: 60vh;
  border-collapse: collapse;
}

.app-table th,
.app-table td {
  padding: 10px;
  border: none;
  position: relative;
}


.app-table th {
  /* border-bottom: 2px solid #ccc !important; */
}

.app-table tr {
  border-bottom: 1px solid #eee;
}

/* Ensuring tbody can hold the overlay */
.tbody-container {
  position: relative;
}
</style>
