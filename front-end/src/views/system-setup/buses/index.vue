<template>
  <div>
    <v-card>
      <v-card-title>
      <v-icon color="primary">
        mdi-bus-multiple
      </v-icon>
        <span class="px-2">{{ $t('buses') }}</span>
        <v-spacer></v-spacer>
        <create-button @create="showBusDialog"></create-button>
        <activation-tool-tip model="buses"></activation-tool-tip>
      </v-card-title>
      <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
        item-key="id"
        :loading="isLoading"
        loading-text="Loading... Please wait"
        :headers="translatedHeaders"
        :items="buses"
        :search="search"
      >
        <template v-slot:top>
          <v-text-field
            v-model="search"
            :label="$t('search')"
            class="mx-4"
          ></v-text-field>
        </template>
        <template v-slot:item.driver="{ item }">
          <v-chip :color="getDriverAssignmentColor(item.driver)" dark @click="assignDriver(item)">
            {{ getDriver(item.driver) }}
          </v-chip>
        </template>
        <template v-slot:item.created_at="{ item }">
          <small>{{ item.created_at | moment("LL") }}</small> -
          <small class="text-muted">{{ item.created_at | moment("LT") }}</small>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon v-if="item.driver" small class="mx-2" @click="unAssignDriver(item)">
            mdi-account-off
          </v-icon>
          <v-icon v-else small class="mx-2" @click="assignDriver(item)">
            mdi-account-tie-hat
          </v-icon>
          <v-icon small class="mx-2" @click="editBus(item)">
            mdi-pencil
          </v-icon>
          <v-icon small class="mx-2" @click="deleteBus(item, buses.indexOf(item))">
            mdi-delete
          </v-icon>
        </template>
      </v-data-table>
    </v-card>
    <v-row justify="center">
      <v-dialog
        v-model="busDialog"
        persistent
        max-width="600px"
      >
        <v-form
          ref="form"
          v-model="valid"
          lazy-validation>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ $t('bus_data') }}</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    sm="6"
                  >
                    <v-text-field
                      v-model="license"
                      :rules="licenseRules"
                      :label="$t('license_plate') + '*'"
                      :hint="$t('license_plate_of_bus')"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="capacity"
                      :rules="capacityRules"
                      :label="$t('capacity') + '*'"
                      :hint="$t('number_of_seats')"
                      required
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="busDialog = false"
              >
                {{ $t('close') }}
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="createBus"
              >
                {{ $t('save') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-dialog>
    </v-row>
    <v-dialog v-if="selectedBus" v-model="driversDialog" max-width="390">
      <v-card>
        <v-card-title class="text-h5">{{ $t('select_driver_for_bus', { license: selectedBus.license }) }}</v-card-title>

        <v-card-text>
          <v-list dense>
            <v-subheader>{{ $t('drivers') }}</v-subheader>
            <v-list-item-group>
              <v-list-item
                v-for="(driver, i) in availableDrivers"
                :key="i"
              >
                <v-list-item-content>
                  <v-list-item-title v-text="driver.name" @click="assignDriverToBus(driver)"></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card-text>
        <v-container style="height: 400px">
          <v-row
            v-show="loadingDrivers"
            class="fill-height"
            align-content="center"
            justify="center"
          >
            <v-col class="text-subtitle-1 text-center" cols="12">
              {{ $t('please_wait') }}
            </v-col>
            <v-col cols="6">
              <v-progress-linear
                :active="loadingDrivers"
                color="primary"
                indeterminate
                rounded
                height="6"
              ></v-progress-linear>
            </v-col>
          </v-row>
        </v-container>
        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            color="green darken-1"
            text
            @click="closeDriverDialog"
          >
            {{ $t('close') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import ActivationToolTip from "@/components/ActivationToolTip";
import CreateButton from "@/components/CreateButton";
import auth from '@/services/AuthService';
export default {
  components: {
    ActivationToolTip,
    CreateButton,
  },
  data() {
    return {
      buses: [],
      availableDrivers: [],
      isLoading: false,
      search: "",
      busDialog: false,
      driversDialog: false,
      loadingDrivers: false,
      valid: true,
      id: null,
      selectedBus: null,
      license: '',
      licenseRules: [
        v => !!v || this.$t('license_plate_required'),
        v => (v && v.length <= 15) || this.$t('license_plate_max_length'),
      ],
      capacity: '',
      capacityRules: [
        v => /^[0-9]+$/.test(v) || this.$t('capacity_required'),
      ],
      headers: [
        { text: "ID", value: "id", align: "start", filterable: false },
        { text: "License", value: "license" },
        { text: "Capacity", value: "capacity" },
        { text: "Driver", value: "driver" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
    };
  },
  mounted() {
    this.loadBuses();
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
    },
    translatedHeaders() {
      return this.headers.map(header => ({
        ...header,
        text: this.$t(header.text.toLowerCase().replace(' ', '_'))
      }));
    },
  },
  methods: {
    loadBuses() {
      this.isLoading = true;
      this.buses = [];
      axios
        .get(`/buses/all`)
        .then((response) => {
          this.buses = response.data;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_buses"),
            type: 'error'
          });
          console.log(error);
          auth.checkError(error.response.data.message, this.$router, this.$swal);
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    validate () {
      return this.$refs.form.validate()
    },
    createBus() {
      if(this.validate())
      {
        this.isLoading = true;
        this.busDialog = false;
        axios
          .post(`/buses/create-edit`, {
            bus: {
              id: this.id,
              license: this.license,
              capacity: this.capacity,
            },
          })
          .then((response) => {
            this.loadBuses();
            this.$notify({
              title: "Success",
              text: this.id? this.$t("bus_updated") : this.$t("bus_created"),
              type: 'success'
            });
            this.$swal(this.$t("success_title"), this.id? this.$t("bus_updated_successfully") : this.$t("bus_created_successfully"), "success");
          })
          .catch((error) => {
            this.$notify({
              title: "Error",
              text: this.$t("error_creating_bus"),
              type: 'error'
            });
            console.log(error);
            this.$swal(this.$t("error_title"), error.response.data.message, "error");
          })
          .then(() => {
            this.isLoading = false;
          });
      }
    },
    showBusDialog() {
      this.license = '';
      this.capacity = '';
      this.id = null;
      this.busDialog = true;
    },
    editBus(bus) {
      this.id = bus.id;
      this.license = bus.license;
      this.capacity = bus.capacity;
      this.busDialog = true;
    },
    deleteBus(bus, index) {
      this.$swal
        .fire({
          title: this.$t("delete_bus"),
          text: this.$t("confirm_delete_bus", { license: bus.license }),
          icon: "error",
          showCancelButton: true,
          cancelButtonText: this.$t("cancel"),
          confirmButtonText: this.$t("yes_delete"),
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.deleteBusServer(bus.id, index);
          }
        });
    },
    deleteBusServer(bus_id, index) {
      axios
        .delete(`/buses/${bus_id}`)
        .then((response) => {
          this.buses.splice(index, 1);
          this.$notify({
            title: "Success",
            text: this.$t("bus_deleted"),
            type: "success",
          });
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_deleting_bus"),
            type: 'error'
          });
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          //this.isDeleting = false;
        });
    },
    getDriverAssignmentColor(driver) {
      if (driver) return "success";
      else return "error";
    },
    getDriver(driver) {
      if (driver) return driver.name;
      else return this.$t("none");
    },
    assignDriver(item) {
      this.selectedBus = item;
      this.driversDialog = true;
      this.loadAvailableDrivers()
    },
    loadAvailableDrivers() {
      this.loadingDrivers = true;
      this.availableDrivers = [];
      axios
        .get('/buses/available-drivers')
        .then((response) => {
          this.availableDrivers = response.data;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_drivers"),
            type: 'error'
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.loadingDrivers = false;
        });
    },
    assignDriverToBus(driver) {
      this.loadingDrivers = true;
      axios
        .post(`/buses/assign-driver`, {
          bus_id: this.selectedBus.id,
          driver_id: driver.id,
        })
        .then((response) => {
          this.loadBuses();
          this.$notify({
            title: "Success",
            text: this.$t("driver_assigned_to_bus"),
            type: 'success'
          });
          this.$swal(this.$t("success_title"), this.$t("driver_assigned_successfully"), "success");
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_assigning_driver"),
            type: 'error'
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.loadingDrivers = false;
          this.closeDriverDialog();
        });
    },
    unAssignDriver(item)
    {
      this.$swal
        .fire({
          title: this.$t("unassign_driver_from_bus"),
          text: this.$t("confirm_unassign_driver", { driver: item.driver.name, license: item.license }),
          icon: "error",
          showCancelButton: true,
          cancelButtonText: this.$t("cancel"),
          confirmButtonText: this.$t("yes_delete"),
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.unAssignDriverFromBus(item);
          }
        });
    },
    unAssignDriverFromBus(item) {
      this.isLoading = true;
      axios
        .post(`/buses/unassign-driver`, {
          bus_id: item.id,
        })
        .then((response) => {
          this.loadBuses();
          this.$notify({
            title: "Success",
            text: this.$t("driver_unassigned_from_bus"),
            type: 'success'
          });
          this.$swal(this.$t("success_title"), this.$t("driver_unassigned_successfully"), "success");
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_unassigning_driver"),
            type: 'error'
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    closeDriverDialog() {
      this.driversDialog = false;
      this.loadingDrivers = false;
      this.availableDrivers = [];
    },
  },
};
</script>
