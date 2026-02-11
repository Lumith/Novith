<template>
  <div>
    <v-card>
      <v-card-title>
      <v-icon color="primary">
        mdi-credit-card-outline
      </v-icon>
        <span class="px-2">{{ $t('payments') }}</span>
        <v-spacer></v-spacer>
        <v-chip color="primary" text-color="white" class="mx-2">
          {{ $t('total') }}: {{ totalPaid }}
        </v-chip>
      </v-card-title>
      <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
        item-key="id"
        :loading="isLoading"
        loading-text="Loading... Please wait"
        :headers="translatedHeaders"
        :items="payments"
        :search="search"
      >
        <template v-slot:top>
          <v-text-field
            v-model="search"
            :label="$t('Search')"
            class="mx-4"
          ></v-text-field>
        </template>

        <template v-slot:item.payer_type="{ item }">
          <v-icon class="mr-2">
            {{ item.payer_type == 'school' ? 'mdi-school' : 'mdi-account' }}
          </v-icon>
          {{ item.payer.name }}
        </template>

        <template v-slot:item.payment_date="{ item }">
          {{ item.payment_date | moment("LL") }}
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import auth from '@/services/AuthService'
export default {
  components: {},
  data() {
    return {
      payments: [],
      totalPaid: 0,
      isLoading: false,
      search: "",
      headers: [
        { text: "id", value: "id", align: "start", filterable: false },
        { text: "plan", value: "plan_name" },
        { text: "paid_price", value: "price" },
        { text: "date", value: "payment_date" },
        { text: "payer", value: "payer_type" },
        // { text: "Actions", value: "actions", sortable: false },
      ],
    };
  },
  computed: {
    translatedHeaders() {
      return this.headers.map(header => ({
        ...header,
        text: this.$t(header.text)
      }));
    },
    footerProps() {
      return {
        itemsPerPageText: this.$t("rows_per_page"),
      }
    }
  },
  mounted() {
    this.loadPayments();
  },
  methods: {
    loadPayments() {
      this.isLoading = true;
      this.payments = [];
      axios
        .get(`/charges/all`)
        .then((response) => {
          this.payments = response.data.charges;
          this.totalPaid = response.data.totalPaid;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_payments"),
            type: "error",
          });
          console.log(error);
          auth.checkError(error.response.data.message, this.$router, this.$swal);
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    viewUpcomingPayment(payment)
    {
      this.$router.push({
        name: "view-upcoming-payment",
        params: {
          user_id: payment.id,
        },
      });
    },
    redeemPayment(index) {
      this.$swal({
        title: this.$t("redeem_payment"),
        html: this.$t("confirm_redeem_payment", {
          amount: this.payments[index].total_amount,
          user_name: this.payments[index].user_name
        }) + "<br/><br/>" + this.showRedemptionDetails(index),
        icon: "warning",
        footer: this.showRedemptionFooter(index),
        showCancelButton: true,
        confirmButtonText: this.$t("yes_redeem"),
      }).then((result) => {
        if (result.isConfirmed) {
          this.redeemPaymentServer(this.payments[index].id, index);
        }
      });
    },

    showRedemptionFooter(index)
    {
      let redemption_preference = this.payments[index].redemption_preference;
      if(redemption_preference==2)
      {
        return '<h2>' + this.$t('bank_account') + '</h2>';
      }
      else if(redemption_preference==3)
      {
        return '<h2>' + this.$t('paypal_account') + '</h2>';
      }
      else if(redemption_preference==4)
      {
        return '<h2>' + this.$t('mobile_money_account') + '</h2>';
      }
      else
      {
        return '<h2>' + this.$t('cash_account') + '</h2>';
      }
    },
    showRedemptionDetails(index)
    {
      let redemption_preference = this.payments[index].redemption_preference;
      console.log(redemption_preference);
      if(redemption_preference==2)
      {
        return '<ul><li><b>' + this.$t('bank_name') + ':</b> ' + this.payments[index].redemption_details.bank_name + '</li>' +
        '<li><b>' + this.$t('account_number') + ':</b> ' + this.payments[index].redemption_details.account_number + '</li>' +
        '<li><b>' + this.$t('beneficiary_name') + ':</b> ' + this.payments[index].redemption_details.beneficiary_name + '</li>' +
        '<li><b>' + this.$t('beneficiary_address') + ':</b> ' + this.payments[index].redemption_details.beneficiary_address + '</li>' +
        (this.payments[index].redemption_details.iban != null ? '<li><b>' + this.$t('iban') + ':</b> ' + this.payments[index].redemption_details.iban + '</li>' : '') +
        (this.payments[index].redemption_details.swift != null ? '<li><b>' + this.$t('swift') + ':</b> ' + this.payments[index].redemption_details.swift + '</li>' : '') +
        (this.payments[index].redemption_details.routing_number != null ? '<li><b>' + this.$t('routing_number') + ':</b> ' + this.payments[index].redemption_details.routing_number + '</li>' : '') +
        (this.payments[index].redemption_details.bic != null ? '<li><b>' + this.$t('bank_identification_code') + ':</b> ' + this.payments[index].redemption_details.bic + '</li>' : '') +
        '</ul>';
      }
      else if(redemption_preference==3)
      {
        return "<b>" + this.$t('paypal') + ":</b> " + this.payments[index].redemption_details.email;
      }
      else if(redemption_preference==4)
      {
        return '<ul><li><b>' + this.$t('phone_number') + ':</b> ' + this.payments[index].redemption_details.phone_number + '</li>' +
        '<li><b>' + this.$t('network') + ':</b> ' + this.payments[index].redemption_details.network + '</li>' +
        '<li><b>' + this.$t('Name') + ':</b> ' + this.payments[index].redemption_details.name + '</li>' +
        '</ul>';
      }
      else
      {
        return "";
      }
    },

    redeemPaymentServer(user_id, index) {
      this.isLoading = true;
      axios
        .post(`/users/redeem`, {
          user_id: user_id,
        })
        .then((response) => {
          this.$notify({
            title: "Success",
            text: this.$t("payment_redeemed"),
            type: "success",
          });
          this.payments.splice(index, 1);
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_redeeming_payment"),
            type: "error",
          });
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

<style lang="scss">
.theme--light.v-list-item:not(.v-list-item--active):not(.v-list-item--disabled):hover{
  cursor: pointer;
  background: rgba($primary-shade--light, 0.15) !important;
}
</style>
