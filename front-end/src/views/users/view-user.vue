<template>
    <div v-if="user">
    <div v-if="loading">
        <v-progress-linear
            indeterminate
            color="primary"
            rounded
            height="5"
            class="mb-0"
        ></v-progress-linear>
    </div>
        <v-card>
            <v-card-title>
                <v-icon color="primary"> mdi-account </v-icon>
                <span class="px-2">
                    {{ $t("Account Information") }}
                </span>
                <v-spacer></v-spacer>
                <v-btn
                    depressed
                    color="secondary"
                    @click="$router.go(-1)"
                    class="mx-1"
                >
                    {{ $t("back") }}
                    <v-icon right dark> mdi-keyboard-return </v-icon>
                </v-btn>
                <v-btn depressed color="primary" @click="editUser" class="mx-1">
                    {{ $t("edit") }}
                    <v-icon right dark> mdi-pencil </v-icon>
                </v-btn>
                <v-btn v-if="userType == 'student'" depressed color="info" @click="emailUser" class="mx-1">
                    {{ $t("email_card") }}
                    <v-icon right dark> mdi-email </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text v-if="user" class="mt-5">
                <!-- display user info -->
                <v-row>
                    <v-col cols="12" md="3" v-if="user.avatar">
                        <div class="d-flex justify-center">
                            <avatar-image-component
                                :edit="false"
                                :avatarUrl="user.avatar"
                                :user="user.id"
                            ></avatar-image-component>
                        </div>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-row class="mx-2">
                            <v-col cols="12" md="6">
                                <p class="font-weight-bold">
                                    {{ $t("User Name") }}
                                </p>
                                <div class="mt-4">
                                    <p>{{ user.name }}</p>
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <p class="font-weight-bold">
                                    {{ $t("Email") }}
                                </p>
                                <div class="mt-4">
                                    <p>{{ user.email }}</p>
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <p class="font-weight-bold">{{ $t("Registered") }}</p>
                                <div class="mt-4">
                                    <p>
                                        {{ user.created_at | moment("LL") }} -
                                        {{ user.created_at | moment("LT") }}
                                    </p>
                                </div>
                            </v-col>
                            <v-col
                                v-if="
                                    userType == 'school' || (userType == 'guardian' && user.role_id==4)
                                "
                                cols="12"
                                md="6"
                            >
                                <p class="font-weight-bold">{{ $t("Balance") }}</p>
                                <div class="mt-4">
                                    <p>{{ user.balance }}</p>
                                </div>
                            </v-col>
                            <v-col
                                v-else-if="userType == 'student'"
                                cols="12"
                                md="6"
                            >
                                <p class="font-weight-bold">{{ $t('student_id') }}</p>
                                <div class="mt-4">
                                    <p>{{ user.student_identification }}</p>
                                </div>
                            </v-col>
                            <v-col v-else cols="12" md="6">
                                <p class="font-weight-bold">{{ $t('phone_number') }}</p>
                                <div class="mt-4">
                                    <p>{{ user.tel_number }}</p>
                                </div>
                            </v-col>
                        </v-row>
                        <v-row class="mx-2">
                            <v-col cols="12" md="6">
                                <p class="font-weight-bold">{{ $t('role') }}</p>
                                <div class="mt-4">
                                    <v-chip color="primary" dark>
                                        {{ getRoleValue(user.role_id) }}
                                        <v-icon class="mx-2">
                                            {{ getIconOfRole(user.role_id) }}
                                        </v-icon>
                                    </v-chip>
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <p class="font-weight-bold">{{ $t('status') }}</p>
                                <div class="mt-4">
                                    <v-chip
                                        :color="getStatusColor(user.status_id)"
                                        dark
                                    >
                                        {{ getStatusValue(user.status_id) }}
                                        <v-icon class="mx-2">
                                            {{
                                                getIconOfStatus(user.status_id)
                                            }}
                                        </v-icon>
                                    </v-chip>
                                </div>
                            </v-col>
                        </v-row>
                        <v-row class="mx-2" v-if="userType == 'student' && user.notes">
                            <v-col cols="12" md="12">
                                <p class="font-weight-bold">{{ $t('notes') }}</p>
                                <div class="mt-4">
                                    {{ user.notes }}
                                </div>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <school-card v-if="user.role_id == 2" class="mt-5" :school="user">
        </school-card>

        <div v-if="user.role_id == 6">
            <v-card-title class="mt-4">
                <v-icon color="primary"> mdi-account-group-outline </v-icon>
                <span class="px-2">{{ $t('guardians') }}</span>
            </v-card-title>
            <div
                v-for="(guardian, index) in user.guardians"
                :key="guardian.id"
                class="mt-5"
            >
            <guardian-card :guardian="guardian"></guardian-card>
            </div>
        </div>

        <div v-if="user.role_id == 4 || user.role_id == 5">
            <v-card-title class="mt-4">
                <v-icon color="primary"> mdi-account-group </v-icon>
                <span class="px-2">{{ $t('students') }}</span>
            </v-card-title>
            <div
                v-for="(student, index) in user.students"
                :key="student.id"
                class="mt-5"
            >
            <student-card :student="student"></student-card>
            </div>
        </div>
        <!-- display driver documents -->
        <v-card
            v-if="user.role_id == 3 && user.driver_information"
            class="mt-5"
        >
            <v-card-title>
                <v-icon color="primary"> mdi-information </v-icon>
                <span class="px-2">{{ $t('personal_information') }}</span>
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('first_name') }}</p>
                        <div class="mt-4">
                            <p>{{ user.driver_information.first_name }}</p>
                        </div>
                    </v-col>
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('last_name') }}</p>
                        <div class="mt-4">
                            <p>{{ user.driver_information.last_name }}</p>
                        </div>
                    </v-col>
                    <!-- phone_number -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">Phone Number</p>
                        <div class="mt-4">
                            <p>{{ user.driver_information.phone_number }}</p>
                        </div>
                    </v-col>
                    <!-- address -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('address') }}</p>
                        <div class="mt-4">
                            <p>{{ user.driver_information.address }}</p>
                        </div>
                    </v-col>
                    <!-- email -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('communication_email') }}</p>
                        <div class="mt-4">
                            <p>{{ user.driver_information.email }}</p>
                        </div>
                    </v-col>
                    <!-- license -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('license') }}</p>
                        <div class="mt-4">
                            <p>{{ user.driver_information.license_number }}</p>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Driver documents -->
        <v-card
            v-if="
                user.role_id == 3 &&
                user.driver_information != null &&
                user.driver_information.documents != null &&
                user.driver_information.documents.length > 0
            "
            class="mt-5"
        >
            <v-card-title>
                <v-icon color="primary"> mdi-file-document </v-icon>
                <span class="px-2">{{ $t('driver_documents') }}</span>
            </v-card-title>
            <v-card-text
                v-for="(document, index) in user.driver_information.documents"
                :key="document.id"
            >
                <v-row>
                    <!-- Document Image -->
                    <v-col cols="12" md="3">
                        <div class="driver-document">
                            <v-avatar
                                rounded
                                size="120"
                                @click="viewDocumentImage(document)"
                            >
                                <v-img
                                    :src="getDocumentImage(document)"
                                    alt="Document Image"
                                ></v-img>
                            </v-avatar>
                        </div>
                    </v-col>
                    <v-col cols="12" md="3">
                        <p class="font-weight-bold">{{ $t('document_name') }}</p>
                        <div class="mt-4">
                            <p>{{ document.document_name }}</p>
                        </div>
                    </v-col>
                    <v-col cols="12" md="3">
                        <p class="font-weight-bold">{{ $t('document_number') }}</p>
                        <div class="mt-4">
                            <p>{{ document.document_number }}</p>
                        </div>
                    </v-col>
                    <v-col cols="12" md="3">
                        <p class="font-weight-bold">{{ $t('expiry_date') }}</p>
                        <div class="mt-4">
                            <p>{{ document.expiry_date | moment("LL") }}</p>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <approve-reject-card v-if="user.role_id == 6" class="mt-5" :userType="$t('student')" @approve-user="approveUser" @reject-user="rejectUser">
        </approve-reject-card>

        <approve-reject-card v-if="user.role_id == 3" class="mt-5" :userType="$t('driver')" @approve-user="approveUser" @reject-user="rejectUser">
        </approve-reject-card>
    </div>
</template>

<script>
import AvatarImageComponent from "../../components/AvatarImageComponent.vue";
import studentCard from "./student-card.vue";
import guardianCard from "./guardian-card.vue";
import schoolCard from "./school-card.vue";
import approveRejectCard from "./approve-reject-card.vue";
import { Keys } from "/src/config.js";
import { adminProfileStore } from "@/utils/helpers";

export default {
    components: {
        AvatarImageComponent,
        Keys,
        studentCard,
        schoolCard,
        guardianCard,
        approveRejectCard
    },
    setup() {
        return { adminProfileStore }
    },
    data() {
        return {
            user: null,
            user_id: null,
            loading: false,
            userType: null,
        };
    },
    mounted() {
        if (this.$route.params.user_id != null) {
            this.user_id = this.$route.params.user_id;
            this.userType = this.$route.name.split("-").slice(-1)[0];
            this.fetchUser();
        }
    },
    methods: {
        fetchUser() {
            this.loading = true;
            axios
                .get(`/users/${this.userType}`, {
                    params: {
                        user_id: this.user_id,
                    },
                })
                .then((response) => {
                    this.loading = false;
                    this.user = response.data;
                    console.log(this.user);
                })
                .catch((error) => {
                    this.loading = false;
                    this.$notify({
                        title: "Error",
                        text: this.$t("error_fetching_user_data"),
                        type: "error",
                    });
                    console.log(error);
                    //this.$router.go(-1);
                });
        },
        emailUser(){
            //api /print-student-card
            this.loading = true;
            axios
                .post('/users/print-student-card', {
                    student_id: this.user.id,
                })
                .then((response) => {
                    this.loading = false;
                    this.$notify({
                        title: "Success",
                        text: this.$t("printing_student_card"),
                        type: "success",
                    });
                    console.log(response);
                })
                .catch((error) => {
                    this.loading = false;
                    this.$notify({
                        title: "Error",
                        text: this.$t("error_printing_student_card"),
                        type: "error",
                    });
                    console.log(error);
                });
        },
        editUser() {
            let routeName = '';
            if(adminProfileStore.id == this.user.id && this.user.role_id == 1)
            {
                routeName = "edit-admin-profile";
            }
            else if(adminProfileStore.id == this.user.id && this.user.role_id == 2)
            {
                routeName = "edit-school-profile";
            }
            else{
                routeName = "edit-" + this.userType;
            }
            this.$router.push({
                name: routeName,
                params: {
                    user_id: this.user.id,
                },
            });
        },
        userStatus(status) {
            if (status == 1) {
                return "Active";
            } else if (status == 2) {
                return "Pending";
            } else if (status == 3) {
                return "Suspended";
            } else if (status == 4) {
                return "Under Review";
            } else {
                return "Unknown";
            }
        },
        getIconOfRedemptionPreference(redemption_preference) {
            if (redemption_preference == 2) {
                return "mdi-bank";
            } else if (redemption_preference == 3) {
                return "mdi-credit-card";
            } else if (redemption_preference == 4) {
                return "mdi-credit-card-multiple";
            } else {
                return "mdi-cash";
            }
        },
        getRedemptionPreferenceColor(redemption_preference) {
            if (redemption_preference == 2) {
                return "primary";
            } else if (redemption_preference == 3) {
                return "info";
            } else if (redemption_preference == 4) {
                return "secondary";
            } else {
                return "success";
            }
        },
        getRedemptionPreferenceValue(redemption_preference) {
            if (redemption_preference == 2) {
                return "Bank";
            } else if (redemption_preference == 3) {
                return "PayPal";
            } else if (redemption_preference == 4) {
                return "Mobile Money";
            } else {
                return "Cash";
            }
        },
        getStatusColor(status) {
            if (status == 1) {
                return "success";
            } else if (status == 2) {
                return "warning";
            } else if (status == 3) {
                return "error";
            } else if (status == 4) {
                return "info";
            }
        },
        getStatusValue(status) {
            if (status == 1) {
                return this.$t("active");
            } else if (status == 2) {
                return this.$t("pending");
            } else if (status == 3) {
                return this.$t("suspended");
            } else if (status == 4) {
                return this.$t("under_review");
            } else {
                return this.$t("unknown");
            }
        },
        getIconOfStatus(status) {
            if (status == 1) {
                return "mdi-check-circle";
            } else if (status == 2) {
                return "mdi-alert-circle";
            } else if (status == 3) {
                return "mdi-close-circle";
            } else if (status == 4) {
                return "mdi-information-outline";
            } else {
                return "mdi-help-circle";
            }
        },
        getRoleValue(role) {
            if (role == 1) {
                return this.$t("admin");
            } else if (role == 2) {
                return this.$t("school");
            } else if (role == 3) {
                return this.$t("driver");
            } else if (role == 4) {
                return this.$t("parent");
            } else if (role == 5) {
                return this.$t("guardian");
            } else if (role == 6) {
                return this.$t("student");
            } else {
                return this.$t("unknown");
            }
        },
        getIconOfRole(role) {
            if (role == 1) {
                return "mdi-account-lock";
            } else if (role == 2) {
                return "mdi-school";
            } else if (role == 3) {
                return "mdi-account-tie-hat";
            } else if (role == 4) {
                return "mdi-account-tie";
            } else if (role == 5) {
                return "mdi-account-group";
            } else if (role == 6) {
                return "mdi-badge-account-outline";
            } else {
                return "mdi-account-question";
            }
        },
        getDocumentImage(document) {
            return Keys.VUE_APP_API_URL + document.remote_file_path;
        },
        viewDocumentImage(document) {
            window.open(
                Keys.VUE_APP_API_URL + document.remote_file_path,
                "_blank"
            );
        },
        rejectUser(userType) {
            this.$swal({
                input: "textarea",
                inputPlaceholder: this.$t("why_rejecting_user", { userType: userType }),
                inputAttributes: {
                    "aria-label": this.$t("why_rejecting_user", { userType: userType }),
                },
                title: this.$t("reject_user", { userType: userType }),
                html: this.$t("confirm_reject_user", { userType: userType }),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t("yes_reject"),
                cancelButtonText: this.$t("cancel"),
            }).then((result) => {
                if (result.isConfirmed) {
                    this.takeActionOnServer(
                        this.user.id,
                        result.value,
                        2
                    );
                }
            });
        },
        approveUser(userType) {
            this.$swal({
                title: this.$t("approve_user", { userType: userType }),
                html: this.$t("confirm_approve_user", { userType: userType }),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t("yes_approve"),
                cancelButtonText: this.$t("cancel"),
            }).then((result) => {
                if (result.isConfirmed) {
                    this.takeActionOnServer(this.user.id, "Approved", 1);
                }
            });
        },
        takeActionOnServer(user_id, reason, action) {
            this.loading = true;
            axios
                .post("/users/take-action", {
                    user_id: user_id,
                    reason: reason,
                    action: action,
                })
                .then((response) => {
                    this.loading = false;
                    this.$notify({
                        title: "Success",
                        text: this.$t("action_performed_successfully"),
                        type: "success",
                    });
                    this.$router.go(-1);
                })
                .catch((error) => {
                    this.loading = false;
                    this.$notify({
                        title: "Error",
                        text: this.$t("error_taking_action"),
                        type: "error",
                    });
                    console.log(error);
                });
        },
    },
};
</script>

<style scoped>
.driver-document {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}
</style>
