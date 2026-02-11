<template>
  <div>
    <v-card>
      <v-card-title>
      <v-icon color="primary">
        mdi-account-check
      </v-icon>
        <span class="px-2">{{ $t('activation') }}</span>
      </v-card-title>
      <v-card-text>
        <div>
            <span class="font-weight-bold">{{ $t('note') }}:</span> {{ $t('activation_note') }} <a href="https://auth.creativeapps.info" target="_blank">{{ $t('here') }}</a>
        </div>
      </v-card-text>

      <!-- text filed for activation code -->
      <v-card-text>
        <v-text-field
          v-model="activationCode"
          :label="$t('activation_code')"
          outlined
          dense
          :rules="activationCodeRules"
        ></v-text-field>
      </v-card-text>

      <!-- activate button -->
      <v-card-actions>
        <v-btn
          color="primary"
          @click="activate"
          :loading="isLoading"
          :disabled="!activationCode"
        >
          {{ $t('activate_button') }}
        </v-btn>
      </v-card-actions>


    </v-card>
  </div>
</template>

<script>

import {
  mdiStopCircleOutline,
  mdiAccountCheck,
  mdiAccountClock,
  mdiAccountOff,
  mdiPlayCircleOutline,
  mdiTrashCan,
  mdiDeleteRestore,
  mdiAirplane,
  mdiMotionPause
} from "@mdi/js";

import { activationStore } from "@/utils/helpers";
export default {
  components: {

  },
  setup() {
    return { activationStore }
  },
  data() {
    return {
      activationCode: null,
      isLoading: false,
      activationCodeRules: [
        (v) => !!v || this.$t("activation_code_required"),
      ],
      icons: {
        mdiStopCircleOutline,
        mdiAccountCheck,
        mdiAccountOff,
        mdiAccountClock,
        mdiPlayCircleOutline,
        mdiTrashCan,
        mdiDeleteRestore,
        mdiAirplane
      },
    };
  },
  mounted() {
    this.loadActivationCode();
  },
  methods: {
    loadActivationCode() {
      this.isLoading = true;
      axios
        .get(`/activation/get-activation-code`)
        .then((response) => {
          this.activationCode = response.data.secure_key;
        })
        .catch((error) => {
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    activate() {
      this.isLoading = true;
      axios
        .post(`/activation/activate`, {
          activationCode: this.activationCode,
        })
        .then((response) => {
          this.$notify({
            title: this.$t("success_title"),
            text: this.$t("account_activated_successfully"),
            type: "success",
          });
          this.activationStore.isActivated = true;
        })
        .catch((error) => {
          this.$notify({
            title: this.$t("error_title"),
            text: this.$t("error_activating_account"),
            type: "error",
          });
          this.activationStore.isActivated = false;
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>
