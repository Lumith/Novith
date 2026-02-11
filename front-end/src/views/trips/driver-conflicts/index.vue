<template>
  <div>
    <v-card>
      <v-card-title>
        <v-icon color="primary"> mdi-alert-circle-outline </v-icon>
        <span class="px-2">{{ $t('driver_schedule_conflicts') }}</span>
      </v-card-title>
      <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
        item-key="id"
        :loading="isLoading"
        :loading-text="$t('loading_driver_conflicts')"
        :headers="translatedHeaders"
        :items="driverConflicts"
        :search="search"
      >
        <template v-slot:top>
          <v-text-field
            v-model="search"
            :label="$t('search_driver_conflicts')"
            class="mx-4"
          ></v-text-field>
        </template>

        <template v-slot:item.driver="{ item }">
          <a @click.stop="displayDriver(item.driver.id)">{{
            item.driver.name
          }}</a>
        </template>

        <template v-slot:item.intersect_date="{ item }">
          <span>{{ item.intersect_date | moment("LL") }}</span>
        </template>

        <template v-slot:item.current_trip="{ item }">
          <a @click.stop="displayTrip(item.current_trip.id)">{{
            item.current_trip.id
          }}
          <div> <strong>{{ $t('route_label') }}</strong> {{ item.current_trip.route.name }} </div>
            <div> <strong>{{ $t('start_label') }}</strong> {{ item.current_trip.first_stop_time | moment("LT") }} </div>
            <div> <strong>{{ $t('end_label') }}</strong> {{ item.current_trip.last_stop_time | moment("LT") }} </div>
          </a>
        </template>

        <template v-slot:item.current_trip_calender="{ item }">
          <v-btn
            depressed
            small
            color="primary"
            @click="viewTripCalendar(item.current_trip)"
          >
            <v-icon dark> mdi-calendar </v-icon>
          </v-btn>
        </template>

        <template v-slot:item.other_trip="{ item }">
          <a @click.stop="displayTrip(item.other_trip.id)">{{
            item.other_trip.id
          }}
          <div> <strong>{{ $t('route_label') }}</strong> {{ item.other_trip.route.name }} </div>
            <div> <strong>{{ $t('start_label') }}</strong> {{ item.other_trip.first_stop_time | moment("LT") }} </div> <div> <strong>{{ $t('end_label') }}</strong> {{ item.other_trip.last_stop_time | moment("LT") }} </div>
          </a>
        </template>

        <template v-slot:item.other_trip_calender="{ item }">
          <v-btn
            depressed
            small
            color="error"
            @click="viewTripCalendar(item.other_trip)"
          >
            <v-icon dark> mdi-calendar </v-icon>
          </v-btn>
        </template>

      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import {
  mdiStopCircleOutline,
  mdiPlayCircleOutline,
  mdiTrashCan,
  mdiDeleteRestore,
  mdiAirplane,
  mdiMotionPause
} from "@mdi/js";


export default {
  components: {

  },
  data() {
    return {
      driverConflicts: [],
      isLoading: false,
      search: "",
      icons: {
        mdiStopCircleOutline,
        mdiPlayCircleOutline,
        mdiTrashCan,
        mdiDeleteRestore,
        mdiAirplane
      },
      headers: [
        {
          text: "Driver",
          align: "start",
          value: "driver",
        },
        {
          text: "Current Trip",
          sortable: false,
          value: "current_trip",
        },
        {
          text: "Details",
          sortable: false,
          value: "current_trip_calender",
        },
        {
          text: "Conflicting Trip",
          sortable: false,
          value: "other_trip",
        },
        {
          text: "Details",
          sortable: false,
          value: "other_trip_calender",
        },
        {
          text: "Conflict Date",
          value: "intersect_date",
        },
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
    this.loadDriverConflicts();
  },
  methods: {
    displayTrip(trip_id) {
      this.$router.push({
        name: "view-trip",
        params: { trip_id: trip_id },
      });
    },
    displayDriver(driver_id) {
      this.$router.push({
        name: "view-driver",
        params: { user_id: driver_id },
      });
    },
    viewTripCalendar(trip) {
      this.$router.push({
        name: "view-calendar",
        params: {
          trip_id: trip.id,
          suspension_id: 'none',
          },
      });
    },

    loadDriverConflicts() {
      this.isLoading = true;
      this.driverConflicts = [];
      axios
        .get(`/drivers/conflicts`)
        .then((response) => {
          this.driverConflicts = response.data.conflicts;
        })
        .catch((error) => {
          this.$notify({
            title: this.$t("error_title"),
            text: this.$t("error_retrieving_driver_conflicts"),
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
