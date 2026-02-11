<template>
    <div>
        <v-card>
            <v-card-title>
                <v-icon color="primary">
                    mdi-account-cash-outline
                </v-icon>
                <span class="pl-2 pr-2">{{ $t('transfer_coins') }}</span>
                <info-tool-tip
                    :message="$t('transfer_coins_info')"
                ></info-tool-tip>
            </v-card-title>
            <v-data-table
                :footer-props="footerProps"
                :no-data-text="$t('noData')"
                :no-results-text="$t('noResults')"
                item-key="id"
                :loading="isLoading"
                :loading-text="$t('loading_parents')"
                :headers="translatedHeaders"
                :items="parents"
                :search="search"
            >
                <template v-slot:top>
                    <v-text-field
                        v-model="search"
                        :label="$t('search_parents')"
                        class="mx-4"
                    ></v-text-field>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        depressed
                        color="primary"
                        @click="transfer(item)"
                        class="mx-1"
                    >
                        {{ $t('transfer_button') }}
                        <v-icon right dark> mdi-check </v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
import InfoToolTip from "@/components/InfoToolTip";
export default {
    components: {
        InfoToolTip,
    },
    data() {
        return {
            parents: [],
            isLoading: true,
            search: "",
            planDialog: false,
            valid: true,
            id: null,
            planTypeName: "",
            selectedPlan: null,
            headers: [
                { text: "ID", value: "id", align: "start", filterable: false },
                { text: "Email", value: "email" },
                { text: "Name", value: "name" },
                { text: "Balance", value: "balance" },
                { text: "Actions", value: "actions", sortable: false },
            ],
        };
    },
    computed: {
        translatedHeaders() {
            return this.headers.map(header => ({
                ...header,
                text: this.$t(header.text.toLowerCase().replace(' ', '_'))
            }));
        },
        footerProps() {
            return {
                itemsPerPageText: this.$t("rows_per_page"),
            }
        }
    },
    mounted() {
        this.loadParents();
    },
    methods: {
        loadParents() {
            this.isLoading = true;
            this.parents = [];
            axios
                .get('/users/all-parents')
                .then(async (response) => {
                    this.parents = response.data.parents;
                })
                .catch((error) => {
                    this.$notify({
                        title: this.$t("error_title"),
                        text: this.$t("error_retrieving_parents"),
                        type: "error",
                    });
                    console.log(error);
                    this.$swal(this.$t("error_title"), error.response.data.message, "error");
                })
                .then(() => {
                    this.isLoading = false;
                });
        },
        transfer(parent) {
            this.$swal
                .fire({
                    title: this.$t("transfer_coins_title"),
                    text: this.$t("enter_coins_amount", { name: parent.name }),
                    input: "number",
                    inputAttributes: {
                        min: 1,
                    },
                    inputValidator: (value) => {
                        if (!value || value <=0) {
                            return this.$t("enter_valid_number");
                        }
                    },
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: this.$t("transfer_confirm"),
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        let coins = result.value;
                        this.isLoading = true;
                        axios
                            .post("/users/transfer-coins", {
                                parent_id: parent.id,
                                coins: coins,
                            })
                            .then((response) => {
                                this.$notify({
                                    title: this.$t("success_title"),
                                    text: this.$t("coins_transferred_successfully"),
                                    type: "success",
                                });
                                //update parent balance with coins
                                let index = this.parents.findIndex((p) => p.id === parent.id);
                                this.parents[index].balance = Number(this.parents[index].balance) + Number(coins);
                            })
                            .catch((error) => {
                                this.$notify({
                                    title: this.$t("error_title"),
                                    text: this.$t("error_transferring_coins"),
                                    type: "error",
                                });
                                console.log(error);
                                this.$swal(this.$t("error_title"), error.response.data.message, "error");
                            })
                            .then(() => {
                                this.isLoading = false;
                            });
                    }
                });
        },
    },
};
</script>
