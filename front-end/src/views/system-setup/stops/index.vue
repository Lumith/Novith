<template>
  <div>
    <v-card>
      <v-card-title>
      <v-icon color="primary">
        mdi-bus-stop
      </v-icon>
        <span class="px-2">{{ $t('stops') }}</span>
        <v-spacer></v-spacer>
        <create-button @create="createStop"></create-button>
        <activation-tool-tip model="stops"></activation-tool-tip>
      </v-card-title>
      <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
        item-key="id"
        :loading="isLoading"
        loading-text="Loading... Please wait"
        :headers="translatedHeaders"
        :items="stops"
        :search="search"
      >
        <template v-slot:top>
          <v-text-field
            v-model="search"
            :label="$t('search')"
            class="mx-4"
          ></v-text-field>
        </template>

        <template v-slot:item.routes="{ item }">
          <a v-if="item.routes.length" @click.stop="displayRoutes(stops.indexOf(item))">{{ item.routes.length }}</a>
          <span v-else>{{ $t('no_routes') }}</span>
        </template>

        <template v-slot:item.created_at="{ item }">
          <small>{{ item.created_at | moment("LL") }}</small> -
          <small class="text-muted">{{ item.created_at | moment("LT") }}</small>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon small class="mx-2" @click="viewStop(item)">
            mdi-eye
          </v-icon>
          <v-icon small class="mx-2" @click="editStop(item)">
            mdi-pencil
          </v-icon>
          <v-icon class="mx-2" small @click="deleteStop(item, stops.indexOf(item))">
            mdi-delete
          </v-icon>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-if="stops[selectedStop]" v-model="dialog" max-width="290">
      <v-card>
        <v-card-title class="text-h5">
          {{ stops[selectedStop].name }}
        </v-card-title>

        <v-card-text>
          <v-list dense>
            <v-subheader>{{ $t('routes') }}</v-subheader>
            <v-list-item-group>
              <v-list-item
                v-for="(item, i) in stops[selectedStop].routes"
                :key="i"
              >
                <v-icon small class="mx-2">
                  mdi-eye
                </v-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="item.name" @click="viewRoute(item)"></v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn color="green darken-1" text @click="dialog = false">
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

export default {
  components: {
    ActivationToolTip,
    CreateButton,
  },
  data() {
    return {
      stops: [],
      dialog: false,
      isLoading: false,
      selectedStop: null,
      search: "",
      headers: [
        { text: "ID", value: "id", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Address", value: "address", width: 300 },
        { text: "Routes", value: "routes" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
    };
  },
  computed: {
    translatedHeaders() {
      return this.headers.map(header => {
        return {
          ...header,
          text: this.$t(header.text.toLowerCase().replace(' ', '_'))
        };
      });
    },
    footerProps() {
      return {
        itemsPerPageText: this.$t("rows_per_page"),
      }
    }
  },
  mounted() {
    this.loadStops();
  },
  methods: {
    displayRoutes(index) {
      this.selectedStop = index;
      this.dialog = true;
    },
    viewRoute(route) {
      this.$router.push({
        name: "view-route",
        params: { route_id: route.id},
      });
    },
    loadStops() {
      this.isLoading = true;
      this.stops = [];
      axios
        .get(`/stops/all`)
        .then((response) => {
          this.stops = response.data;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_stops"),
            type: "error",
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    createStop() {
      this.$router.push({
        name: "create-stop",
      });
    },
    viewStop(stop)
    {
      this.$router.push({
        name: "view-stop",
        params: {
          stop_id: stop.id,
        },
      });
    },
    editStop(stop)
    {
      this.$router.push({
        name: "edit-stop",
        params: {
          stop_id: stop.id,
        },
      });
    },
    deleteStop(stop, index) {
      if(stop.routes.length>0)
      {
        this.$swal
        .fire({
          title: this.$t("cannot_delete_stop"),
          text: this.$t("cannot_delete_stop_message", { name: stop.name }),
          icon: "error",
        });
      }
      else
      {
        this.$swal
          .fire({
            title: this.$t("delete_stop"),
            text: this.$t("confirm_delete_stop", { name: stop.name }),
            icon: "error",
            showCancelButton: true,
            confirmButtonText: this.$t("yes_delete"),
          })
          .then((result) => {
            if (result.isConfirmed) {
              this.deleteStopServer(stop.id, index);
            }
          });
      }
    },
    deleteStopServer(stop_id, index) {
      axios
        .delete(`/stops/${stop_id}`)
        .then((response) => {
          this.stops.splice(index, 1);
          this.$notify({
            title: "Success",
            text: this.$t("stop_deleted"),
            type: "success",
          });
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_deleting_stops"),
            type: "error",
          });
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          //this.isDeleting = false;
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
