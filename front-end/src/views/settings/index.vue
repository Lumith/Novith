<template>
  <div>
    <v-card>
      <v-card-title>
        <v-icon color="primary"> mdi-cog </v-icon>
        <span class="px-2">{{ $t('settings') }}</span>
      </v-card-title>
      <v-card-text>
        <vue-element-loading :active="isLoading" />
        <v-form ref="form" v-if="settings" v-model="valid" lazy-validation class="my-2">
          <div class="my-4">
            <label class="text--secondary font-weight-bold">{{ $t('general_settings') }}</label>
          </div>
          <v-row>
            <v-col cols="12" md="4" class="my-2">
              <v-autocomplete
                v-model="selectedCurrency"
                :items="currencies"
                dense
                outlined
                item-text="name"
                item-value="id"
                :label="$t('currency')"
                :filter="customFilter"
                persistent-hint
                return-object
                required
                :rules="requiredRules"
                :hint="
                  selectedCurrency != null
                    ? selectedCurrency.code + ' (' + selectedCurrency.symbol + ')'
                    : ''
                "
                @change="currencySelected"
              ></v-autocomplete>
            </v-col>
            <!-- distance_to_stop_to_mark_arrived -->
            <v-col cols="12" md="4" class="my-2">
                <v-text-field
                  v-model="settings.distance_to_stop_to_mark_arrived"
                  type="number"
                  outlined
                  dense
                  :label="$t('distance_to_mark_arrived')"
                  :placeholder="$t('enter_distance_arrived')"
                  persistent-hint
                  required
                  :rules="numberRules"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="my-2">
                <v-text-field
                  v-model="settings.max_search_radius"
                  type="number"
                  outlined
                  dense
                  :label="$t('max_distance_to_stop')"
                  :placeholder="$t('enter_max_distance')"
                  persistent-hint
                  required
                  :rules="numberRules"
                ></v-text-field>
            </v-col>
          </v-row>
          <div class="my-4">
            <label class="text--secondary font-weight-bold">{{ $t('driver_app_announcement_settings') }}</label>
          </div>
          <v-row>
            <v-col cols="12" md="4" class="my-2">
                <v-text-field
                  v-model="settings.distance_to_drop_off"
                  type="number"
                  outlined
                  dense
                  :label="$t('announcement_distance_drop_off')"
                  :placeholder="$t('enter_announcement_distance_drop_off')"
                  persistent-hint
                  required
                  :rules="numberRules"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="my-2">
                <v-text-field
                  v-model="settings.distance_to_pick_up"
                  type="number"
                  outlined
                  dense
                  :label="$t('announcement_distance_pick_up')"
                  :placeholder="$t('enter_announcement_distance_pick_up')"
                  persistent-hint
                  required
                  :rules="numberRules"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="my-2">
                <v-text-field
                  v-model="settings.distance_to_slow_down"
                  type="number"
                  outlined
                  dense
                  :label="$t('announcement_distance_slow_down')"
                  :placeholder="$t('enter_announcement_distance_slow_down')"
                  persistent-hint
                  required
                  :rules="numberRules"
                ></v-text-field>
            </v-col>
          </v-row>
          <div class="mt-8">
            <label class="text--secondary font-weight-bold">{{ $t('admin_information') }}
                <info-tool-tip class="ml-2"
            :message="$t('admin_info_displayed_in_apps')"></info-tool-tip>
            </label>
          </div>
          <v-row class="my-2">
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="settings.admin_email"
                    outlined
                    dense
                    :label="$t('admin_email')"
                    :placeholder="$t('enter_admin_email')"
                    persistent-hint
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="settings.admin_phone"
                    outlined
                    dense
                    :label="$t('admin_phone')"
                    :placeholder="$t('enter_admin_phone')"
                    persistent-hint
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="settings.admin_address"
                    outlined
                    dense
                    :label="$t('admin_address')"
                    :placeholder="$t('enter_admin_address')"
                    persistent-hint
                ></v-text-field>
            </v-col>
          </v-row>
          <div class="mt-8">
            <label class="text--secondary font-weight-bold">{{ $t('allow_otp_setting') }}             <info-tool-tip class="ml-2"
            :message="$t('otp_tooltip')"></info-tool-tip></label>
          </div>
          <v-row>
            <v-col cols="12" md="6">
              <v-checkbox
                v-model="settings.otp_required"
                :label="$t('allow_otp')"
                persistent-hint
              ></v-checkbox>
            </v-col>
          </v-row>
          <div class="mt-8">
            <label class="text--secondary font-weight-bold">{{ $t('hide_payment_screen_parent_app') }}             <info-tool-tip class="ml-2"
            :message="$t('hide_payment_tooltip')"></info-tool-tip></label>
          </div>
          <v-row>
            <v-col cols="12" md="6">
              <v-checkbox
                v-model="settings.hide_payment_parents"
                :label="$t('hide_payment_screen_parent_app')"
                persistent-hint
              ></v-checkbox>
            </v-col>
          </v-row>
          <div class="mt-8">
            <label class="text--secondary font-weight-bold">{{ $t('hide_schools_setting') }}             <info-tool-tip class="ml-2"
            :message="$t('hide_schools_tooltip')"></info-tool-tip></label>
          </div>
          <v-row>
            <v-col cols="12" md="6">
              <v-checkbox
                v-model="settings.hide_schools"
                :label="$t('hide_schools')"
                persistent-hint
              ></v-checkbox>
            </v-col>
          </v-row>
          <div class="mt-8">
            <label class="text--secondary font-weight-bold">{{ $t('mode_setting') }}             <info-tool-tip class="ml-2"
            :message="$t('simple_mode_tooltip')"></info-tool-tip></label>
          </div>
          <v-row>
            <v-col cols="12" md="6">
              <v-checkbox
                v-model="settings.simple_mode"
                :label="$t('simple_mode')"
                persistent-hint
              ></v-checkbox>
            </v-col>
          </v-row>
          <div class="mt-8">
            <label class="text--secondary font-weight-bold">{{ $t('ads_settings') }} <info-tool-tip class="ml-2"
            :message="$t('ads_settings_tooltip')"> </info-tool-tip></label>
          </div>
          <v-row>
            <v-col cols="12" md="6">
              <v-checkbox
                v-model="settings.allow_ads_in_driver_app"
                :label="$t('allow_ads_driver_app')"
                persistent-hint
              ></v-checkbox>
              <v-checkbox
                v-model="settings.allow_ads_in_parent_app"
                :label="$t('allow_ads_customer_app')"
                persistent-hint
              ></v-checkbox>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn :disabled="!valid" color="primary" @click="saveSettings">
            {{ $t('save') }}
            <v-icon right dark> mdi-content-save </v-icon>
          </v-btn>
        </v-card-actions>
    </v-card>
  </div>
</template>

<script>
import InfoToolTip from "@/components/InfoToolTip";
import VueElementLoading from "vue-element-loading";
import auth from '@/services/AuthService'
export default {
  components: {
    VueElementLoading,
    InfoToolTip
  },
  data() {
    return {
      currencies: [],
      isLoading: false,
      settings: null,
      valid: true,
      requiredRules: [(v) => !!v || this.$t("required")],
      numberRules: [(v) => /^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$/.test(v) || this.$t("must_be_greater_than_0")],
      percentRules: [(v) => /^100$|^[0-9]{1,2}$|^[0-9]{1,2}\,[0-9]{1,3}$/.test(v) || this.$t("must_be_0_to_100")],
      selectedCurrency: null,
    };
  },
  mounted() {
    this.loadSettings();
  },
  methods: {
    customFilter (item, queryText, itemText) {
      const textOne = item.name.toLowerCase()
      const textTwo = item.code.toLowerCase()
      const searchText = queryText.toLowerCase()

      return textOne.indexOf(searchText) > -1 ||
        textTwo.indexOf(searchText) > -1
    },
    //API Calls
    saveSettings() {
      this.submiting = true;
      axios
        .post("/settings/update", this.settings)
        .then((response) => {
          this.submiting = false;
          this.$notify({
            title: "Success",
            text: this.$t("settings_updated"),
            type: "success",
          });
        })
        .catch((error) => {
          this.submiting = false;
          this.$notify({
            title: "Error",
            text: this.$t("error_updating_settings"),
            type: "error",
          });
          console.log(error);
        });
    },
    loadSettings() {
      this.isLoading = true;
      this.settings = [];
      axios
        .get(`/settings/all`)
        .then((response) => {
          this.settings = response.data;
          this.loadCurrencies();
          this.selectedCurrency = this.settings.currency;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_settings"),
            type: "error",
          });
          console.log(error);
          auth.checkError(error.response.data.message, this.$router, this.$swal);
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    loadCurrencies() {
      this.isLoading = true;
      this.currencies = [];
      axios
        .get(`/currencies/all`)
        .then((response) => {
          this.currencies = response.data;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_currencies"),
            type: "error",
          });
          console.log(error);
          this.$swal("Error", error.response.data.message, "error");
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    validate() {
      this.valid = false;
      let v = this.$refs.form.validate();
      if (v) {
        this.valid = true;
        return true;
      }
      return false;
    },
    currencySelected(r) {
      this.selectedCurrency = r;
      this.settings.currency_id = this.selectedCurrency.id;
    },
  },
};
</script>
