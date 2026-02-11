<template>
<div>
  <vue-element-loading :active="isLoading" :is-full-screen="true" />
  <v-card :loading="isLoading" id="account-setting-card">
    <v-card-title>
      <span v-if="user != null" class="me-3">{{ user.name }}</span>
      <v-spacer></v-spacer>
      <v-btn depressed color="secondary" @click="$router.go(-1)" class="mx-1">
        {{ $t('back') }}
        <v-icon right dark> mdi-keyboard-return </v-icon>
      </v-btn>
    </v-card-title>
    <div v-if="userType == 'admin' || userType == 'school'">
        <!-- tabs -->
        <v-tabs v-model="tab" show-arrows>
        <v-tab v-for="tab in translatedTabs" :key="tab.icon">
            <v-icon size="20" class="me-3">
            {{ tab.icon }}
            </v-icon>
            <span>{{ tab.title }}</span>
        </v-tab>
        </v-tabs>
        <!-- tabs item -->
        <v-tabs-items v-model="tab">
        <v-tab-item>
            <account-settings-account
            :account-data="user"
            :user-type="userType"
            @save-user-info="saveUser"
            ></account-settings-account>
        </v-tab-item>

        <v-tab-item>
            <account-settings-security @save-user-password="savePassword"></account-settings-security>
        </v-tab-item>
        </v-tabs-items>
    </div>
    <div v-else>
        <account-settings-account
        :account-data="user"
        :user-type="userType"
        @save-user-info="saveUser"
        ></account-settings-account>
    </div>
  </v-card>
</div>
</template>

<script>
import {
  mdiAccountOutline,
  mdiLockOpenOutline,
} from "@mdi/js";
import { ref } from '@vue/composition-api'

import AccountSettingsAccount from "./user-settings/AccountSettingsAccount.vue";
import AccountSettingsSecurity from "./user-settings/AccountSettingsSecurity.vue";

import VueElementLoading from "vue-element-loading";
import { adminProfileStore } from "@/utils/helpers";
export default {
  components: {
    AccountSettingsAccount,
    AccountSettingsSecurity,
    VueElementLoading,
  },
  setup() {
    return { adminProfileStore }
  },
  data() {
    return {
      isLoading: false,
      user_id: null,
      userType: null,
      user: null,
      edit: false,
      tabs: [
        { title: "account", icon: mdiAccountOutline },
        { title: "change_password", icon: mdiLockOpenOutline },
      ],
      tab: ref(""),
    };
  },
  watch: {
    $route(to, from) {
      console.log(to);
      this.onRouteChanged();
    },
  },
  computed: {
    translatedTabs() {
      return this.tabs.map((tab) => {
        return {
          title: this.$t(tab.title),
          icon: tab.icon,
        };
      });
    },
  },
  mounted() {
    this.onRouteChanged();
  },
  methods: {
    onRouteChanged()
    {
      if (this.$route.params.user_id != null) {
        this.user_id = this.$route.params.user_id;
        this.userType = this.$route.name.split("-")[1];
        this.fetchUser();
      }
    },
    //API Calls
    fetchUser() {
      this.isLoading = true;
      axios
        .get(`/users/${this.userType}`, {
          params: {
            user_id: this.user_id,
          },
        })
        .then((response) => {
          this.isLoading = false;
          this.user = response.data;
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: this.$t("error_title"),
            text: this.$t("error_fetching_user_data"),
            type: "error",
          });
          console.log(error);
          this.$router.go(-1);
        });
    },
    saveUser() {
      this.isLoading = true;
      axios
        .post(`/users/${this.userType}-edit`, {
          user: this.user,
        })
        .then((response) => {
            if(this.userType == 'admin' || this.userType == 'school')
            {
                adminProfileStore.name = this.user.name;
            }
          this.isLoading = false;
          this.$notify({
            title: this.$t("success_title"),
            text: this.$t("user_updated"),
            type: "success",
          });
          this.$router.go(-1);
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: this.$t("error_title"),
            text: this.$t("error_updating_user"),
            type: "error",
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        });
    },
    savePassword(password)
    {
      this.isLoading = true;
      axios
        .post("/users/update-password", {
          user_id: this.user.id,
          password: password,
        })
        .then((response) => {
          this.isLoading = false;
          this.$notify({
            title: this.$t("success_title"),
            text: this.$t("user_updated"),
            type: "success",
          });
          this.$router.go(-1);
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: this.$t("error_title"),
            text: this.$t("error_updating_user"),
            type: "error",
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        });
    },
  },
};
</script>
