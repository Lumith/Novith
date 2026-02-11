<template>
  <v-row v-if="!isLoading">

    <v-col
      cols="12"
      sm="12"
      md="6"
    >
      <dashboard-card-total-earning :amount=totalAdminEarnings></dashboard-card-total-earning>
    </v-col>

    <v-col
      cols="12"
      md="6"
      sm="12"
    >
      <v-row class="match-height">
        <v-col
          cols="12"
          sm="6"
        >
          <statistics-card-vertical
            :change="translatedTotalFromSchools.change"
            :color="translatedTotalFromSchools.color"
            :icon="translatedTotalFromSchools.icon"
            :statistics="translatedTotalFromSchools.amount"
            :stat-title="translatedTotalFromSchools.statTitle"
            :subtitle="translatedTotalFromSchools.subtitle"
          ></statistics-card-vertical>
        </v-col>
        <v-col
          cols="12"
          sm="6"
        >
          <statistics-card-vertical
            :change="translatedTotalFromParents.change"
            :color="translatedTotalFromParents.color"
            :icon="translatedTotalFromParents.icon"
            :statistics="translatedTotalFromParents.amount"
            :stat-title="translatedTotalFromParents.statTitle"
            :subtitle="translatedTotalFromParents.subtitle"
          ></statistics-card-vertical>
        </v-col>
      </v-row>
    </v-col>

    <v-col
      cols="12"
      md="12"
    >
      <dashboard-statistics-card :all-counts="translatedAllCounts"></dashboard-statistics-card>
    </v-col>



    <v-col
      cols="12"
      sm="12"
      md="6"
    >
      <dashboard-weekly-overview
      :trip-count=plannedTripsCount
      :trip-dates=plannedTripsDates
      ></dashboard-weekly-overview>
    </v-col>
    <v-col
      cols="12"
      md="6"
    >
      <dashboard-card-plans
      :best-plans=bestSalesPlans
      ></dashboard-card-plans>
    </v-col>
  </v-row>
  <v-row v-else>
    <v-col
      cols="12"
      sm="6"
      md="6"
    >
      <v-skeleton-loader
        type="card"
        height="200"
      ></v-skeleton-loader>
    </v-col>
    <v-col
      cols="12"
      md="6"
    >
      <v-row class="match-height">
        <v-col
          cols="12"
          sm="6"
        >
          <v-skeleton-loader
            type="card"
            height="200"
          ></v-skeleton-loader>
        </v-col>
        <v-col
          cols="12"
          sm="6"
        >
          <v-skeleton-loader
            type="card"
            height="200"
          ></v-skeleton-loader>
        </v-col>
      </v-row>
    </v-col>

    <v-col
      cols="12"
      md="12"
    >
      <v-skeleton-loader
        type="card"
        height="120"
      ></v-skeleton-loader>
    </v-col>

    <v-col
      cols="12"
      sm="6"
      md="6"
    >
      <v-skeleton-loader
        type="image"
        height="500"
      ></v-skeleton-loader>
    </v-col>

    <v-col
      cols="12"
      md="6"
    >
      <v-skeleton-loader
        type="image"
        height="500"
      ></v-skeleton-loader>
    </v-col>

  </v-row>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import { mdiPoll, mdiCurrencyUsd, mdiCloseOctagonOutline, mdiCard, mdiSchool, mdiAlien, mdiAccountAlert, mdiAccountBox, mdiAccountCash } from '@mdi/js'
import StatisticsCardVertical from '@/components/statistics-card/StatisticsCardVertical.vue'
import auth from '@/services/AuthService'
// demos
import DashboardStatisticsCard from './DashboardStatisticsCard.vue'
import DashboardCardTotalEarning from './DashboardCardTotalEarning.vue'
import DashboardCardPlans from './DashboardCardPlans.vue'
import DashboardWeeklyOverview from './DashboardWeeklyOverview.vue'

export default {
  components: {
    StatisticsCardVertical,
    DashboardStatisticsCard,
    DashboardCardTotalEarning,
    DashboardCardPlans,
    DashboardWeeklyOverview,
  },
  data() {
    return {
      isLoading: false,
      totalFromSchools: {
        statTitle: 'schools_payments',
        icon: mdiSchool,
        color: 'success',
        amount: '',
      },
      bestSalesPlans: [],
      plannedTripsCount: [],
      plannedTripsDates: [],
      bestSalesPlansColors: [
        'success', 'error', 'warning', 'secondary', 'error',
      ],
      totalFromParents: {
        statTitle: 'parents_payments',
        icon: mdiAccountCash,
        color: 'info',
        amount: '',
      },
      totalAdminEarnings: null,
      allCounts:
      [
        {
          title: 'Schools',
          total: '',
        },
        {
          title: 'Guardians',
          total: '',
        },
        {
          title: 'Drivers',
          total: '',
        },
        {
          title: 'Students',
          total: '',
        },
        {
          title: 'Routes',
          total: '',
        },
        {
          title: 'Stops',
          total: '',
        },
      ],
    }
  },
  computed: {
    translatedTotalFromSchools() {
      return {
        ...this.totalFromSchools,
        statTitle: this.$t(this.totalFromSchools.statTitle)
      };
    },
    translatedTotalFromParents() {
      return {
        ...this.totalFromParents,
        statTitle: this.$t(this.totalFromParents.statTitle)
      };
    },
    translatedAllCounts() {
      return this.allCounts.map(count => ({
        ...count,
        title: this.$t(count.title.toLowerCase())
      }));
    }
  },
  mounted() {
    this.fetchDashboardStatistics()
  },
  methods: {
    fetchDashboardStatistics() {
      this.isLoading = true;
      this.reservations = [];
      axios
        .get(`/admin-dashboard/all`)
        .then((response) => {
          this.totalFromSchools.amount = response.data.paidSchools;
          this.totalFromParents.amount = response.data.paidParents;
        //   this.totalRefunds.amount = response.data.totalRefunds;
          this.totalAdminEarnings = response.data.totalPaid;
          this.allCounts[0].total = response.data.totalSchools;
          this.allCounts[1].total = response.data.totalGuardians;
          this.allCounts[2].total = response.data.totalDrivers;
          this.allCounts[3].total = response.data.totalStudents;
          this.allCounts[4].total = response.data.totalRoutes;
          this.allCounts[5].total = response.data.totalStops;
          this.bestSalesPlans = response.data.bestSalesPlans;
          //merge bestSalesPlans with bestSalesPlansColors
          this.bestSalesPlans.forEach((item, index) => {
            item.color = this.bestSalesPlansColors[index];
          });
          for (var key in response.data.plannedTrips) {
            this.plannedTripsCount.push(response.data.plannedTrips[key]);
            this.plannedTripsDates.push(key);
          }
          console.log(this.bestSalesPlans);
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_dashboard_statistics"),
            type: "error",
          });
          console.log(error);
          auth.checkError(error.response.data.message, this.$router, this.$swal);
        })
        .then(() => {
          this.isLoading = false;
        });
    },
  },
}
</script>

<style lang="scss">
.v-skeleton-loader__image {
    height: 400px !important;
}
</style>
