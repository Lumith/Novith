<template>
  <div>
    <v-card>
      <v-card-title>
      <v-icon color="primary">
        mdi-road-variant
      </v-icon>
        <span class="px-2">{{ $t('routes') }}</span>
        <v-spacer></v-spacer>
        <v-sheet
            elevation="3"
            rounded="lg"
            class="text-center mx-auto">
            <div class="mx-4">
                <v-radio-group
                v-model="routes_type"
                row
                >
                    <v-radio
                        :label="$t('all_routes')"
                        value="all"
                    ></v-radio>
                    <v-radio
                        :label="$t('morning_routes')"
                        value="morning"
                    ></v-radio>
                    <v-radio
                        :label="$t('afternoon_routes')"
                        value="afternoon"
                    ></v-radio>
                </v-radio-group>
            </div>
        </v-sheet>
        <v-spacer></v-spacer>
        <v-menu
            offset-y>
            <template v-slot:activator="{ on, attrs }">
                <v-btn depressed
                :disabled="!activationStore.isActivated"
                class="mx-2"
                v-bind="attrs"
                v-on="on"
                color="primary">
                    {{ $t('create') }}
                    <v-icon right dark> mdi-plus-thick </v-icon>
                </v-btn>
            </template>
            <v-list>
                <v-list-item
                v-for="(item, index) in menu_items"
                :key="index"
                :value="index"
                @click="createRoute(item.title)"
                >
                <v-list-item-icon>
                    <v-icon>{{ item.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
        <activation-tool-tip model="routes"></activation-tool-tip>
      </v-card-title>
      <v-data-table
:footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
        item-key="name"
        :loading="isLoading"
        loading-text="Loading... Please wait"
        :headers="translatedHeaders"
        :items="displayedRoutes"
        :search="search"
      >
        <template v-slot:top>
          <v-text-field
            v-model="search"
            :label="$t('Search')"
            class="mx-4"
          ></v-text-field>
        </template>

        <template v-slot:item.created_at="{ item }">
          <small>{{ item.created_at | moment("LL") }}</small> -
          <small class="text-muted">{{ item.created_at | moment("LT") }}</small>
        </template>
        <template v-slot:item.is_morning="{ item }">
          <v-chip :color="getTimeColor(item.is_morning)" dark>
            {{ getTime(item.is_morning) }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" small class="mr-4" @click="viewRoute(item)">
                mdi-eye
              </v-icon>
            </template>
            <span>{{ $t('view_route') }}</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" small class="mx-2" @click="editRoute(item, 'duplicate-new')">
                mdi-content-duplicate
              </v-icon>
            </template>
            <span>{{ $t('duplicate_as_new') }}</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-if="item.is_morning" v-bind="attrs" v-on="on" small class="ml-2" @click="editRoute(item, 'duplicate-reverse')">
                mdi-moon-waning-crescent
              </v-icon>
            </template>
            <span>{{ $t('duplicate_as_afternoon') }}</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-if="!item.is_morning" v-bind="attrs" v-on="on" small class="ml-2" @click="editRoute(item, 'duplicate-reverse')">
                mdi-white-balance-sunny
              </v-icon>
            </template>
            <span>{{ $t('duplicate_as_morning') }}</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" small class="ml-2" @click="deleteRoute(item, displayedRoutes.indexOf(item))">
                mdi-delete
              </v-icon>
            </template>
            <span>{{ $t('delete_route') }}</span>
          </v-tooltip>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import ActivationToolTip from "@/components/ActivationToolTip";
import { activationStore } from "@/utils/helpers";

export default {
  components: {
    ActivationToolTip,
    activationStore,
  },
  setup() {
    return { activationStore }
  },
  data() {
    return {
        routes_type: "all",
        menu_items: [
          { title: 'morning', icon: 'mdi-weather-sunset-up' },
          { title: 'afternoon', icon: 'mdi-weather-sunset-down' },
        ],
      routes: [],
      displayedRoutes: [],
      isLoading: false,
      search: "",
      headers: [
        { text: "ID", value: "id", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Stops", value: "stops_count" },
        { text: "Time", value: "is_morning" },
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
  watch: {
    routes_type: function (newVal, oldVal) {
      this.filterRoutes(newVal);
    }
  },
  mounted() {
    this.loadRoutes();
  },
  methods: {
    filterRoutes(routes_type)
    {
        this.routes_type = routes_type;
        if(routes_type == "all")
        {
            this.displayedRoutes = this.routes;
        }
        else if(routes_type == "morning")
        {
            this.displayedRoutes = this.routes.filter(route => route.is_morning == 1);
        }
        else if(routes_type == "afternoon")
        {
            this.displayedRoutes = this.routes.filter(route => route.is_morning == 0);
        }
    },
    loadRoutes() {
      this.isLoading = true;
      this.routes = [];
      this.displayedRoutes = [];
      axios
        .get(`/routes/all`)
        .then((response) => {
          this.routes = response.data;
          this.displayedRoutes = response.data;
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_retrieving_routes"),
            type: 'error'
          });
          console.log(error);
          this.$swal(this.$t("error_title"), error.response.data.message, "error");
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    getTimeColor(is_morning) {
      return is_morning ? "success" : "warning";
    },
    getTime(is_morning) {
      return is_morning ? this.$t("morning") : this.$t("afternoon");
    },
    createRoute(title) {
      this.$swal
        .fire({
          title: this.$t("enter_route_name", { type: this.$t(title.toLowerCase()) }),
          input: "text",
            inputPlaceholder: this.$t("new_route"),
          showCancelButton: true,
          cancelButtonText: this.$t("cancel"),
          confirmButtonText: this.$t("ok"),
        })
        .then((result) => {
          if (result.isConfirmed) {
            const value = result.value.trim();
            this.$router.push({
              name: "create-route",
              params: { route_name: value ? value : "Untitled", route_type: title },
            });
          }
        });
    },
    viewRoute(route) {
      this.$router.push({
        name: "view-route",
        params: { route_id: route.id},
      });
    },
    editRoute(route, action = "edit") {
      this.$swal
        .fire({
          title: this.$t("enter_route_name_simple"),
          input: "text",
          inputValue: route.name,
          showCancelButton: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            const value = result.value.trim();
            this.$router.push({
              name: "edit-route",
              params: { route_id: route.id, new_route_name: value ? value : "Untitled", action: action },
            });
          }
        });

    },
    deleteRoute(route, index) {
      this.$swal
        .fire({
          title: this.$t("delete_route"),
          text: this.$t("confirm_delete_route", { name: route.name }),
          icon: "error",
          showCancelButton: true,
          confirmButtonText: this.$t("yes_delete"),
          cancelButtonText: this.$t("cancel"),
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.deleteRouteServer(route.id, index);
          }
        });
    },
    deleteRouteServer(route_id, index) {
      axios
        .delete(`/routes/${route_id}`)
        .then((response) => {
          let idx = this.routes.findIndex((route) => route.id == route_id);
          this.routes.splice(idx, 1);
          this.filterRoutes(this.routes_type);
          this.$notify({
            title: "Success",
            text: this.$t("route_deleted"),
            type: "success",
          });
        })
        .catch((error) => {
          this.$notify({
            title: "Error",
            text: this.$t("error_deleting_routes"),
            type: 'error'
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
