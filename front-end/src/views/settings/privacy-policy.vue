<template>
  <div>
    <v-card>
      <v-card-title>
        <v-icon color="primary"> mdi-shield-account </v-icon>
        <span class="px-2">{{ $t('Privacy Policy') }}</span>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="savePrivacyPolicy">
          {{ $t('save') }}
          <v-icon right dark> mdi-content-save </v-icon>
        </v-btn>
        <v-btn class="mx-2" depressed color="success" @click="previewPrivacy">
          {{ $t('preview') }}
          <v-icon right dark> mdi-eye </v-icon>
        </v-btn>
      </v-card-title>
      <div class="px-6">
        <p>{{ $t('generate_privacy_policy') }} <a href="https://www.privacypolicygenerator.info/" target="_blank">here</a></p>
        </div>
      <v-card-text>
      <vue-element-loading :active="isLoading" />
        <v-form ref="form" v-if="privacyPolicy" class="my-2">
          <v-row>
            <v-col cols="12" md="12" class="my-2">
              <v-textarea
                v-model="privacyPolicy"
                outlined
                dense
                rows="30"
                row-height="30"
                :label="$t('Privacy Policy')"
                :placeholder="$t('enter_privacy_policy')"
                persistent-hint
                required
              ></v-textarea>
          </v-col>
        </v-row>
        </v-form>

      </v-card-text>
    </v-card>
  </div>
</template>

<script>

import VueElementLoading from "vue-element-loading";

export default {
  components: {
    VueElementLoading,
  },
  data() {
    return {
      isLoading: false,
      privacyPolicy: null,
    };
  },
  mounted() {
    this.getPrivacy();
  },
  methods: {
    previewPrivacy() {
      //open privacy in new tab
      const routeData = this.$router.resolve({
        name: "privacy",
        params: { privacy: this.privacyPolicy },
      });
      window.open(routeData.href, '_blank');
    },
    getPrivacy() {
      this.isLoading = true;
      axios
        .get("/settings/privacy-policy")
        .then((response) => {
          this.isLoading = false;
          this.privacyPolicy = response.data.privacy;
          console.log(this.privacyPolicy);
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_privacy_policy"),
            type: "error",
          });
        });
    },
    savePrivacyPolicy() {
      this.isLoading = true;
      axios
        .post("/settings/update-privacy-policy", {
          privacy: this.privacyPolicy,
        })
        .then((response) => {
          this.isLoading = false;
          this.$notify({
            title: "Success",
            text: this.$t("privacy_policy_saved"),
            type: "success",
          });
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: "Error",
            text: this.$t("error_saving_privacy_policy"),
            type: "error",
          });
        });
    },
  },
};
</script>
