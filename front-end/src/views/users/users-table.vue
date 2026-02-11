<template>
  <div>
    <v-data-table
        :footer-props="footerProps"
        :no-data-text="$t('noData')"
        :no-results-text="$t('noResults')"
        item-key="id"
        :loading="isLoading"
        loading-text="Loading... Please wait"
        :headers="getHeader()"
        :items="users"
        :search="search"
      >
        <template v-slot:top>
          <v-text-field
            v-model="search"
            :label="$t('Search')"
            class="mx-4"
          ></v-text-field>
        </template>

        <template v-slot:item.status_id="{ item }">
          <v-chip
            :color="getStatusColor(item.status_id)"
            dark
          >
            {{ getStatusValue(item.status_id) }}
          </v-chip>
        </template>

        <template v-slot:item.name="{ item }">
            {{item.name}}
            <v-tooltip v-if="item.expired != null && item.expired"  bottom color="info">
                <template v-slot:activator="{ on }">
                <v-icon color="error" v-on="on">
                    mdi-cancel
                </v-icon>
                </template>
                <span>{{ $t('driver_documents_expired') }}</span>
            </v-tooltip>
        </template>
        <template v-if="userType === 'guardians'" v-slot:item.role_id="{ item }">
                    <v-chip
                        dark
                        :color="item.role_id === 4? 'primary' : 'info'"
                    >
                        {{ item.role_id === 4? $t('parent') : $t('guardian') }}
                    </v-chip>
        </template>
        <template v-if="userType === 'drivers'" v-slot:item.bus="{ item }">
          <v-chip
            dark
            @click="$emit('assignBus', item)"
            :color="getBusAssignmentColor(item.bus)"
          >
            {{ item.bus != null? item.bus.license : $t('no_bus') }}
          </v-chip>
        </template>

        <template v-slot:item.created_at="{ item }">
          <small>{{ item.created_at | moment("LL") }}</small> -
          <small class="text-muted">{{ item.created_at | moment("LT") }}</small>
        </template>
        <template v-slot:item.student_settings.morning_bus="{ item }">
          <v-tooltip v-if="tab<2 && userType === 'students' && mode === 'simple'" bottom>
            <template v-slot:activator="{ on, attrs }">
                <v-chip
                    dark
                    @click="$emit('set-student-bus', item, 1)"
                    :color="getBusAssignmentColor(item.student_settings.morning_bus)"
                >
                    {{ item.student_settings.morning_bus != null? item.student_settings.morning_bus.license : $t('no_bus') }}
                </v-chip>
            </template>
            <span>{{ $t('set_morning_bus') }}</span>
          </v-tooltip>
        </template>
        <template v-slot:item.student_settings.afternoon_bus="{ item }">
          <v-tooltip v-if="tab<2 && userType === 'students' && mode === 'simple'" bottom>
            <template v-slot:activator="{ on, attrs }">
                <v-chip
                    dark
                    @click="$emit('set-student-bus', item, 0)"
                    :color="getBusAssignmentColor(item.student_settings.afternoon_bus)"
                >
                    {{ item.student_settings.afternoon_bus != null? item.student_settings.afternoon_bus.license : $t('no_bus') }}
                </v-chip>
            </template>
            <span>{{ $t('set_afternoon_bus') }}</span>
          </v-tooltip>
        </template>


        <template v-slot:item.actions="{ item }">
          <v-tooltip v-if="tab==2" :key="'take-action-'+tab" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-bind="attrs"
                v-on="on"
                small
                class="mx-2 primary"
                @click="$emit('view-user', item)"
              >
                <v-icon small class="mx-2">
                  mdi-card-account-mail-outline
                </v-icon>
                {{ $t('Take Action') }}
              </v-btn>
            </template>
            <span>{{ $t('approve_or_reject') }}</span>
          </v-tooltip>
          <v-tooltip v-else :key="'eye-'+tab" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('view-user', item)">
                mdi-eye
              </v-icon>
            </template>
            <span>{{ $t('view') }}</span>
          </v-tooltip>
          <v-tooltip v-if="tab==0" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-if="checkIfLocationEnabled(item) && mode === 'simple'" v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('show-student-location', item)">
                mdi-map-marker
              </v-icon>
            </template>
            <span>{{ $t('pickup_and_drop_off_locations') }}</span>
          </v-tooltip>
          <v-tooltip v-if="tab<2"  bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('edit-user', item)">
                mdi-pencil
              </v-icon>
            </template>
            <span>{{ $t('edit') }}</span>
          </v-tooltip>
          <v-tooltip v-if="tab<2" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('suspend-user', item, users.indexOf(item))">
               {{item.status_id==3? icons.mdiAccountCheck: icons.mdiAccountOff}}
              </v-icon>
            </template>
            <span>{{item.status_id==3? $t('activate_user') : $t('suspend_user') }}</span>
          </v-tooltip>
          <v-tooltip v-if="tab!=2" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-if="userType === 'drivers' && item.bus" v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('unassign-bus', item)">
                mdi-fridge-industrial-off
              </v-icon>
              <v-icon v-else-if="userType === 'drivers'" v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('assign-bus', item)">
                mdi-bus
              </v-icon>
            </template>
            <span>{{item.bus? $t('unassign_bus_title') : $t('assign_bus') }}</span>
          </v-tooltip>
          <v-tooltip v-if="tab!=2" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-if="userType === 'schools'" v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('login-as-school', item)">
                mdi-login
              </v-icon>
            </template>
            <span>{{ $t('login_as_this_school') }}</span>
          </v-tooltip>
          <v-tooltip v-if="tab!=2" bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon v-if="userType === 'schools'" v-bind="attrs" v-on="on" small class="mx-2" @click="$emit('delete-school', item)">
                mdi-delete
              </v-icon>
            </template>
            <span>{{ $t('delete_this_school') }}</span>
          </v-tooltip>

        </template>
    </v-data-table>
  </div>
</template>

<script>
import {
  mdiAccountCheck,
  mdiAccountOff,
} from "@mdi/js";

export default {
  props: {
    users: Array,
    userType: String,
    tab: Number,
    mode: String,
  },
  components: {},
  data() {
    return {
      isLoading: false,
      search: "",
      defaultsHeaders: [
        { text: "id", value: "id", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Email", value: "email" },
        { text: "Status", value: "status_id" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      driverHeaders: [
        { text: "id", value: "id", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Email", value: "email" },
        { text: "Bus", value: "bus" },
        { text: "Status", value: "status_id" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      studentAdvancedHeaders: [
        { text: "id", value: "student_identification", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Parent Name", value: "parent.name" },
        { text: "Parent Email", value: "parent.email" },
        { text: "Status", value: "status_id" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      studentSimpleHeaders: [
        { text: "id", value: "student_identification", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Parent Name", value: "parent.name" },
        { text: "Parent Email", value: "parent.email" },
        //morning bus
        { text: "Morning Bus", value: "student_settings.morning_bus" },
        //afternoon bus
        { text: "Afternoon Bus", value: "student_settings.afternoon_bus" },
        { text: "Status", value: "status_id" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      parentHeaders: [
        { text: "id", value: "id", align: "start", filterable: false },
        { text: "Name", value: "name" },
        { text: "Email", value: "email" },
        { text: "Role", value: "role_id" },
        { text: "Status", value: "status_id" },
        { text: "Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false },
      ],
      statuses: [
        { value: "Active", color: "success" },
        { value: "Pending", color: "warning" },
        { value: "Suspended", color: "error" },
        { value: "Under Review", color: "error" },
        { value: "Out of Coins", color: "error" },
      ],
      icons: {
        mdiAccountCheck,
        mdiAccountOff,
      },
    };
  },
  computed: {
    translatedDefaultsHeaders() {
      return this.defaultsHeaders.map(header => ({
        ...header,
        text: this.$t(header.text)
      }));
    },
    translatedDriverHeaders() {
      return this.driverHeaders.map(header => ({
        ...header,
        text: this.$t(header.text.toLowerCase().replace(' ', '_'))
      }));
    },
    translatedStudentAdvancedHeaders() {
      return this.studentAdvancedHeaders.map(header => ({
        ...header,
        text: this.$t(header.text.replace(' ', '_'))
      }));
    },
    translatedStudentSimpleHeaders() {
      return this.studentSimpleHeaders.map(header => ({
        ...header,
        text: this.$t(header.text.replace(' ', '_').replace('.', '_'))
      }));
    },
    translatedParentHeaders() {
      return this.parentHeaders.map(header => ({
        ...header,
        text: this.$t(header.text)
      }));
    },
    translatedStatuses() {
      return this.statuses.map(status => ({
        ...status,
        value: this.$t(status.value.toLowerCase().replace(' ', '_'))
      }));
    },
    footerProps() {
      return {
        itemsPerPageText: this.$t("rows_per_page"),
      }
    }
  },
  methods: {
    getStatusColor(status)
    {
      return this.statuses[status-1].color;
    },
    getStatusValue(status)
    {
      return this.translatedStatuses[status-1].value;
    },
    getBusAssignmentColor(bus)
    {
        return bus != null? 'info': 'error';
    },
    getHeader()
    {
      if(this.userType === 'drivers') return this.translatedDriverHeaders;
      if(this.userType === 'students')
      {
            if(this.mode === 'simple'){
                return this.translatedStudentSimpleHeaders;
            }else{
                return this.translatedStudentAdvancedHeaders;
            }
      }
      if(this.userType === 'guardians') return this.translatedParentHeaders;
      return this.translatedDefaultsHeaders;
    },
    checkIfLocationEnabled(item)
    {
        return this.userType === 'students' && item.student_settings != null && (item.student_settings.pickup_lat != null && item.student_settings.pickup_lng != null || item.student_settings.drop_off_lat != null && item.student_settings.drop_off_lng != null);
    }
  },
};
</script>
<style lang="scss">
.theme--light.v-list-item:not(.v-list-item--active):not(.v-list-item--disabled):hover{
  cursor: pointer;
  background: rgba($primary-shade--light, 0.15) !important;
}
</style>
