<template>
  <div>
    <v-card>
      <v-card-title>
        <v-icon color="primary"> mdi-file-document-edit-outline </v-icon>
        <span class="px-2">{{ $t('Terms & Conditions') }}</span>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="saveTerms">
          {{ $t('save') }}
          <v-icon right dark> mdi-content-save </v-icon>
        </v-btn>
        <v-btn class="mx-2" depressed color="success" @click="previewTerms">
          {{ $t('preview') }}
          <v-icon right dark> mdi-eye </v-icon>
        </v-btn>
      </v-card-title>
      <div class="px-6">
        <p>{{ $t('generate_terms') }} <a href="https://www.termsandconditionsgenerator.com" target="_blank">here</a></p>
        </div>
      <v-card-text>
      <vue-element-loading :active="isLoading" />
        <v-form ref="form" v-if="terms" class="my-2">
          <v-row>
            <v-col cols="12" md="12" class="my-2">
              <v-textarea
                v-model="terms"
                outlined
                dense
                rows="30"
                row-height="30"
                :label="$t('terms_and_conditions')"
                :placeholder="$t('enter_terms_conditions')"
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
      terms: null,
    };
  },
  mounted() {
    this.getTerms();
  },
  methods: {
    previewTerms() {
      //open terms in new tab
      const routeData = this.$router.resolve({
        name: "terms",
      });
      window.open(routeData.href, '_blank');
    },
    getTerms() {
      this.isLoading = true;
      axios
        .get("/settings/terms")
        .then((response) => {
          this.isLoading = false;
          this.terms = response.data.terms;
          console.log(this.terms);
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_terms"),
            type: "error",
          });
        });
    },
    saveTerms() {
      this.isLoading = true;
      axios
        .post("/settings/update-terms", {
          terms: this.terms,
        })
        .then((response) => {
          this.isLoading = false;
          this.$notify({
            title: "Success",
            text: this.$t("terms_updated"),
            type: "success",
          });
        })
        .catch((error) => {
          this.isLoading = false;
          this.$notify({
            title: "Error",
            text: this.$t("error_saving_terms"),
            type: "error",
          });
        });
    },
  },
};
</script>
