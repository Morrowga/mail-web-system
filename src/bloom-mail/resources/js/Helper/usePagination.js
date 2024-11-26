/**
 * Represents a paginated response for information data.
 *
 * @typedef {Object} InformationResponse
 * @property {number} current_page - The current page number.
 * @property {any[]} data - An array of information objects.
 * @property {string} first_page_url - URL of the first page.
 * @property {number} from - The starting index of the items on the current page.
 * @property {number} last_page - The last page number.
 * @property {string} last_page_url - URL of the last page.
 * @property {Link[]} links - Pagination links.
 * @property {string|null} next_page_url - URL of the next page (null if there is no next page).
 * @property {string} path - The base path for the pagination.
 * @property {number} per_page - The number of items per page.
 * @property {string|null} prev_page_url - URL of the previous page (null if there is no previous page).
 * @property {number} to - The ending index of the items on the current page.
 * @property {number} total - The total number of items.
 */

import { router } from "@inertiajs/vue3";
import { defineModel, reactive } from "vue";

/**
 * Custom hook to manage pagination.
 *
 * @param {InformationResponse | undefined} pagination - The pagination data object.
 */
const usePagination = (pagination) => {
    const paginationData = reactive({
        data: [],
        nextpage: null,
        currentPage: pagination.current_page,
        previousPage: null,
    });

    const page = defineModel({
        type: String,
        required: true,
        default: () => null,
    });

    if (pagination) {
        paginationData.data = pagination.data;
        paginationData.nextpage = pagination.next_page_url;
        paginationData.previousPage =
            pagination.current_page < 1 ? pagination.current_page - 1 : null;
        paginationData.currentPage = pagination.current_page;
        paginationData.previousPage = pagination.prev_page_url;
    }

    const fetchData = () => {
        console.log("reach...");
        router.get(`/information?page=${paginationData.currentPage}`);
    };

    return {
        data: paginationData.data,
        nextPage: paginationData.nextpage,
        previousPage: paginationData.previousPage,
        currentPage: paginationData.currentPage,
        total: pagination.total,
        lastPage: pagination.last_page,
        fetchData,
        path: pagination.path,
        page,
    };
};

export default usePagination;
