<template>
    <div>
        <v-card>
            <v-card-title>
                <v-icon color="primary">
                    {{
                        planType == 0
                            ? "mdi-alpha-s-box-outline"
                            : "mdi-alpha-p-box-outline"
                    }}
                </v-icon>
                <span class="pl-2 pr-2">{{getPlanTitle()}}</span>
                <info-tool-tip
                    :message="$t('one_coin_track_student')"
                ></info-tool-tip>
                <v-spacer></v-spacer>
                <create-button @create="showCreatePlansDialog"></create-button>
            </v-card-title>
            <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
                item-key="id"
                :loading="isLoading"
                :loading-text="$t('loading_text')"
                :headers="translatedHeaders"
                :items="plans"
                :search="search"
            >
                <template v-slot:top>
                    <v-text-field
                        v-model="search"
                        :label="$t('Search')"
                        class="mx-4"
                    ></v-text-field>
                </template>
                <template v-slot:item.availability="{ item }">
                    <v-chip
                        :color="getAvailabilityColor(item.availability)"
                        dark
                    >
                        {{ getAvailabilityValue(item.availability) }}
                    </v-chip>
                </template>
                <template v-slot:item.created_at="{ item }">
                    <small>{{ item.created_at | moment("LL") }}</small> -
                    <small class="text-muted">{{
                        item.created_at | moment("LT")
                    }}</small>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mx-2" @click="editPlan(item)">
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        small
                        @click="deletePlan(item, plans.indexOf(item))"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>
        </v-card>
        <v-row justify="center">
            <v-dialog v-model="planDialog" persistent max-width="700px">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-card>
                        <v-card-title>
                            <span class="text-h5">{{ $t('plan_data') }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field
                                            v-model="name"
                                            :rules="nameRules"
                                            :label="$t('Name') + '*'"
                                            :hint="$t('name_of_plan')"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field
                                            v-model="coin_count"
                                            :rules="coinRules"
                                            :label="$t('coins') + '*'"
                                            :hint="$t('number_coins_plan')"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field
                                            v-model="price"
                                            :rules="priceRules"
                                            :label="$t('Price') + '*'"
                                            :hint="$t('price_of_plan')"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-select
                                            v-model="availability"
                                            :items="translatedAvailabilityTypes"
                                            :rules="[v => !!v || $t('availability_required')]"
                                            :label="$t('availability')"
                                            required
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="planDialog = false"
                            >
                                {{ $t('close') }}
                            </v-btn>
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="createPlan"
                            >
                                {{ $t('save') }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
import InfoToolTip from "@/components/InfoToolTip";
import CreateButton from "@/components/CreateButton";
import auth from '@/services/AuthService';
export default {
    components: {
        InfoToolTip,
        CreateButton,
    },
    data() {
        return {
            plans: [],
            planType: "",
            availability: null,
            availableDrivers: [],
            isLoading: false,
            search: "",
            planDialog: false,
            valid: true,
            id: null,
            planTypeName: "",
            selectedPlan: null,
            name: "",
            nameRules: [
                (v) => !!v || this.$t("plan_name_required"),
                (v) =>
                    (v && v.length <= 15) ||
                    this.$t("plan_name_max_length"),
            ],
            coin_count: "",
            coinRules: [
                (v) => /^[0-9]+$/.test(v) || this.$t("coins_not_valid"),
            ],
            price: "",
            priceRules: [(v) => /^[0-9]+$/.test(v) || this.$t("price_not_valid")],
            headers: [
                { text: "id", value: "id", align: "start", filterable: false },
                { text: "Name", value: "name" },
                { text: "coins", value: "coin_count" },
                { text: "Price", value: "price" },
                { text: "availability", value: "availability" },
                { text: "Created", value: "created_at" },
                { text: "Actions", value: "actions", sortable: false },
            ],
            availability_types: [
                { text: "one_time_purchase", value: 1 },
                { text: "multiple_purchases", value: 2 },
            ],
        };
    },
    mounted() {
        this.planTypeName = this.$route.name.split("-")[0];
        this.planType = this.planTypeName == "school" ? 0 : 1;
        this.loadPlans();
        //capitalize first letter
        this.planTypeName =
            this.planTypeName.charAt(0).toUpperCase() +
            this.planTypeName.slice(1);
    },
    computed: {
        translatedHeaders() {
            return this.headers.map(header => ({
            ...header,
            text: this.$t(header.text)
            }));
        },
        translatedAvailabilityTypes() {
            return this.availability_types.map(type => ({
            ...type,
            text: this.$t(type.text)
            }));
        },
        footerProps() {
            return {
                itemsPerPageText: this.$t("rows_per_page"),
            }
        }
    },
    methods: {
        loadPlans() {
            this.isLoading = true;
            this.plans = [];
            axios
                .get(`/plans/all`, {
                    params: {
                        planType: this.planType,
                    },
                })
                .then((response) => {
                    this.plans = response.data;
                })
                .catch((error) => {
                    this.$notify({
                        title: "Error",
                        text: this.$t("error_retrieving_plans"),
                        type: "error",
                    });
                    console.log(error);
                    auth.checkError(error.response.data.message, this.$router, this.$swal);
                })
                .then(() => {
                    this.isLoading = false;
                });
        },
        validate() {
            return this.$refs.form.validate();
        },
        getPlanTitle() {
            console.log(this.planTypeName);
            if (this.planTypeName == "School") {
                return this.$t("school_plans");
            } else {
                return this.$t("parent_plans");
            }
        },
        createPlan() {
            if (this.validate()) {
                this.isLoading = true;
                this.planDialog = false;
                axios
                    .post(`/plans/create-edit`, {
                        plan: {
                            id: this.id,
                            name: this.name,
                            coin_count: this.coin_count,
                            plan_type: this.planType,
                            price: this.price,
                            availability: this.availability,
                        },
                    })
                    .then((response) => {
                        this.loadPlans();
                        this.$notify({
                            title: "Success",
                            text: this.id ? this.$t("plan_updated") : this.$t("plan_created"),
                            type: "success",
                        });
                        this.$swal(
                            this.$t("success_title"),
                            this.$t("plan_operation_success", { operation: this.id ? this.$t("updated") : this.$t("created") }),
                            "success"
                        );
                    })
                    .catch((error) => {
                        this.$notify({
                            title: "Error",
                            text: this.$t("error_creating_plan"),
                            type: "error",
                        });
                        console.log(error);
                        this.$swal(
                            this.$t("error_title"),
                            error.response.data.message,
                            "error"
                        );
                    })
                    .then(() => {
                        this.isLoading = false;
                    });
            }
        },
        showCreatePlansDialog() {
            this.name = "";
            this.coin_count = "";
            this.price = "";
            this.id = null;
            this.availability = null;
            this.planDialog = true;
        },
        editPlan(plan) {
            this.id = plan.id;
            this.name = plan.name;
            this.coin_count = plan.coin_count;
            this.price = plan.price;
            this.availability = plan.availability;
            this.planDialog = true;
        },
        deletePlan(plan, index) {
            this.$swal
                .fire({
                    title: this.$t("delete_plan"),
                    text: this.$t('confirm_delete_plan', { name: plan.name }),
                    icon: "error",
                    showCancelButton: true,
                    cancelButtonText: this.$t("cancel"),
                    confirmButtonText: this.$t("yes_delete"),
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.deletePlanServer(plan.id, index);
                    }
                });
        },
        deletePlanServer(plan_id, index) {
            axios
                .delete(`/plans/${plan_id}`)
                .then((response) => {
                    this.plans.splice(index, 1);
                    this.$notify({
                        title: this.$t("success_title"),
                        text: this.$t("plan_deleted"),
                        type: "success",
                    });
                })
                .catch((error) => {
                    this.$notify({
                        title: this.$t("error_title"),
                        text: this.$t("error_deleting_plans"),
                        type: "error",
                    });
                    this.$swal(this.$t("error_title"), error.response.data.message, "error");
                })
                .then(() => {
                    //this.isDeleting = false;
                });
        },
        getAvailabilityColor(availability) {
            return availability == 1 ? "info" : "success";
        },
        getAvailabilityValue(availability) {
            return availability == 1
                ? this.$t(this.availability_types[0].text)
                : this.$t(this.availability_types[1].text);
        },
    },
};
</script>
