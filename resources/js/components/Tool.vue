<template>
    <div class="relative">
        <div
                v-if="loading"
                class="bg-white flex items-center justify-center absolute pin z-50"
        >
            <loader class="text-60" />
        </div>

        <div>
            <div class="py-3 flex items-center border-b border-50">
                <div class="flex items-center ml-auto px-3">
                        <dropdown
                            class="bg-30 hover:bg-40 rounded"
                        >
                            <dropdown-trigger slot-scope="{toggle}" :handle-click="toggle" class="px-3">
                                <icon type="filter" class="text-80" />
                            </dropdown-trigger>

                            <dropdown-menu slot="menu" width="290" direction="rtl" :dark="true">
                                <!-- Per Page -->
                                <filter-select>
                                    <h3 slot="default" class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
                                        Per Page:
                                    </h3>

                                    <select slot="select"
                                        dusk="per-page-select"
                                        class="block w-full form-control-sm form-select"
                                        v-model="limit"
                                        @change="limitChanged"
                                    >
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                    </select>
                                </filter-select>
                            </dropdown-menu>
                        </dropdown>
                </div>
            </div>
            <table class="w-full table" v-if="faults.length > 0">
                <thead>
                    <tr>
                        <th class="text-left">Error</th>
                        <th class="text-left">Environment</th>
                        <th class="text-center">
                            <sortable-icon
                                @sort="requestOrderByChange"
                                resource-name="honeybadger"
                                uri-key="frequent"
                            >Count
                            </sortable-icon>
                        </th>
                        <th class="text-left">
                            <sortable-icon
                                @sort="requestOrderByChange"
                                resource-name="honeybadger"
                                uri-key="recent"
                            >Last
                            </sortable-icon>
                        </th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="fault in faults">
                        <td>
                            <h4 class="py-2">
                                <a target="_blank" :href="fault.url" class="text-primary">{{ fault.klass }}</a>
                            </h4>
                            <p class="py-2">{{ fault.message }}</p>
                        </td>
                        <td class="text.center">{{ fault.environment }}</td>
                        <td class="text-center whitespace-no-wrap">{{ fault.notices_count }}</td>
                        <td class="whitespace-no-wrap">{{ fault.last_notice_at | humanTime }}</td>
                        <td>
                            <span v-if="fault.resolved">Resolved</span>
                            <span v-else>Unresolved</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="flex justify-center items-center px-6 py-8">
                <div class="text-center">
                    <h3 class="text-base text-80 font-normal mb-6">
                        No faults found.
                    </h3>
                </div>
            </div>

            <pagination-links
                v-if="resourceResponse"
                :resource-name="resourceName"
                :resources="faults"
                :resource-response="resourceResponse"
                @previous="selectPreviousPage"
                @next="selectNextPage">
            </pagination-links>
        </div>
    </div>
</template>

<script>
    import {InteractsWithQueryString} from 'laravel-nova'

    export default {
        props: ['resourceName', 'resourceId', 'field'],

        mixins: [
            InteractsWithQueryString,
        ],

        data() {
            return {
                currentOrderByDirection: 'desc',
                limit: 10,
                order: 'recent',
                loading: false,
                links: [],
                faults: [],
            }
        },

        filters: {
            humanTime(string) {
                return moment.utc(string).fromNow();
            }
        },

        mounted() {
            this.fetchFaults();

            this.orderByField('recent');
        },

        computed: {
            resourceResponse() {
                return {
                    prev_page_url: this.links.prev,
                    next_page_url: this.links.next,
                };
            }
        },

        methods: {

            async fetchFaultUrl(url) {
                this.loading = true;

                const {data} = await Nova.request().get(`/nova-vendor/honeybadgerio/honeybadger-laravel-nova/url`, {
                    params: {
                        url: url
                    }
                });

                this.loading = false;

                this.faults = data.results || [];
                this.links = data.links || [];
            },

            async fetchFaults() {
                this.loading = true;

                const {data} = await Nova.request().get(`/nova-vendor/honeybadgerio/honeybadger-laravel-nova/${this.resourceName}/${this.resourceId}`, {
                    params: {
                        order: this.order,
                        limit: this.limit,
                        contextKey: this.field.contextKey,
                        contextValue: this.field.contextValue,
                        contextAttribute: this.field.contextAttribute,
                        searchString: this.field.searchString,
                    }
                });

                this.loading = false;

                this.faults = data.results || [];
                this.links = data.links || [];
            },

            /**
             * Sort the resources by the given column.
             */
            orderByField(column) {
                this.updateQueryString({
                    'honeybadger_order': column,
                    'honeybadger_direction': 'desc',
                })
            },

            requestOrderByChange(sort) {
                this.orderByField(sort.key);

                this.order = sort.key;

                this.fetchFaults();
            },

            selectPreviousPage() {
                this.fetchFaultUrl(this.links.prev);
            },

            selectNextPage() {
                this.fetchFaultUrl(this.links.next);
            },

            limitChanged() {
                this.fetchFaults();
            }

        }
    }
</script>
