<template>
  <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
    item-key="name"
    :loading="loading"
    :loading-text="$t('loading_planned_trips')"
    :headers="translatedHeaders"
    :items="plannedTrips"
    :search="search"
  >
    <template v-slot:top>
      <v-text-field
        v-model="search"
        :label="$t('search_planned_trips')"
        class="mx-4"
      ></v-text-field>
    </template>

    <template v-slot:item.trip.route.name="{ item }">
        <div v-if="item.trip.route">
            <a @click.stop="displayRoute(item.trip.route.id)">{{
                item.trip.route.name
            }}</a>
            <v-chip :color="getTimeColor(item.trip.route.is_morning)" dark class="ml-2">
                {{ getTime(item.trip.route.is_morning) }}
            </v-chip>
        </div>
        <div v-else>
            <v-chip color="error" dark>
                {{ $t('no_route') }}
            </v-chip>
        </div>
    </template>

    <template v-slot:item.driver.name="{ item }">
      <a @click.stop="displayDriver(item.driver.id)">{{
        item.driver.name
      }}</a>
    </template>

    <template v-slot:item.bus.license="{ item }">
      <a v-if="item.bus" @click.stop="displayBus(item.bus)">{{
        item.bus.license
      }}</a>
    </template>

    <template v-slot:item.planned_date="{ item }">
      <small>{{ item.planned_date | moment("LL") }}</small>
    </template>

    <template v-slot:item.planned_start_date_time="{ item }">
      <small class="text-muted">{{ item.planned_start_date_time | moment("LT") }}</small>
    </template>

    <template v-slot:item.planned_end_date_time="{ item }">
      <small class="text-muted">{{ item.planned_end_date_time | moment("LT") }}</small>
    </template>

    <template v-slot:item.started_at="{ item }">
      <small class="text-muted">{{ item.started_at | moment("LT") }}</small>
    </template>

    <template v-slot:item.ended_at="{ item }">
      <small class="text-muted">{{ item.ended_at | moment("LT") }}</small>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-btn v-if="showNotification && item.reservations_count >0" depressed small density="compact" color="info"
      :icon="true"
      @click="sendNotification(item)">
        <v-icon dark> mdi-bell </v-icon>
      </v-btn>
    </template>
  </v-data-table>
</template>

<script>

export default {
  props: {
    plannedTrips: Array,
    showStart: {
      type: Boolean,
      default: false,
    },
    showEnd: {
      type: Boolean,
      default: false,
    },
    showNotification: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    mode: {
      type: String,
      default: null,
    },
  },
  components: {},
  data() {
    return {
      search: "",
      advanced_headers: [
        { text: "Planned Date", value: "planned_date", align: "start"},
        { text: "Driver", value: "driver.name" },
        { text: "Bus", value: "bus.license"},
        { text: "Route", value: "trip.route.name"},
        { text: "Planned Start", value: "planned_start_date_time" },
        { text: "Planned End", value: "planned_end_date_time" },
        { text: "Students", value: "reservations_count" },
        { text: " ", value: "actions", sortable: false},
      ],
      simple_headers: [
        { text: "Planned Date", value: "planned_date", align: "start"},
        { text: "Driver", value: "driver.name" },
        { text: "Bus", value: "bus.license"},
        { text: "Students", value: "reservations_count" },
        { text: " ", value: "actions", sortable: false},
      ],
      headers:[],
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
    if(this.mode === "advanced"){
      this.headers = this.advanced_headers;
    }else{
        this.headers = this.simple_headers;
    }
    let finalHeaders = this.headers;
    if (this.showStart) {
      finalHeaders.push({ text: this.$t("started_at"), value: "started_at" });
    }
    if (this.showEnd) {
      finalHeaders.push({ text: this.$t("ended_at"), value: "ended_at" });
    }
    this.headers = finalHeaders;
  },
  methods: {
    displayRoute(route_id) {
      this.$router.push({
        name: "view-route",
        params: { route_id: route_id },
      });
    },
    displayDriver(driver_id) {
      this.$router.push({
        name: "view-driver",
        params: { user_id: driver_id, },
      });
    },
    getTimeColor(is_morning) {
      return is_morning ? "success" : "warning";
    },
    getTime(is_morning) {
      return is_morning ? this.$t("morning") : this.$t("afternoon");
    },
    displayBus(bus) {
      //show swal with bus info
      this.$swal({
        title: this.$t("bus_info"),
        html: `
          <div class="text-left">
            <p><b>${this.$t('license_label')}</b> ${bus.license}</p>
            <p><b>${this.$t('capacity_label')}</b> ${bus.capacity}</p>
          </div>
        `,
        showCancelButton: false,
        focusConfirm: false,
        confirmButtonText: this.$t("ok"),
      });
    },
    sendNotification(trip) {
      this.$emit("send-notification", trip);
    },
  },
};
</script>
