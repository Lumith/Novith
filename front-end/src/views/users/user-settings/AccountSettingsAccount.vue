<template>
  <v-card v-if="accountData != null" flat class="pa-3 mt-2">
    <v-card-text class="d-flex">
      <!-- upload photo -->
      <div>
        <avatar-image-component :edit="adminProfileStore.id == accountData.id" :avatarUrl="accountData.avatar" :user="accountData"></avatar-image-component>
      </div>
    </v-card-text>

    <v-card-text>
      <v-form
        ref="form"
        v-model="valid"
        class="multi-col-validation mt-6"
        lazy-validation
      >
        <v-row>
          <v-col md="6" cols="12">
            <v-text-field
              v-model="accountData.name"
              :label="$t('Name')"
              dense
              outlined
              required
              :rules="nameRules"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              v-model="accountData.email"
              :label="$t('Email')"
              :disabled="accountData.id != 1 && userType !== 'student'"
              dense
              outlined
              required
              :rules="emailRules"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-select
            disabled
              v-model="accountData.role_id"
              dense
              outlined
              :label="$t('Role')"
              :items="translatedRoles"
              item-text="role"
              item-value="value"
            ></v-select>
          </v-col>

          <v-col cols="12" md="6">
            <v-select
              v-model="accountData.status_id"
              :disabled="accountData.id == 1"
              dense
              outlined
              :label="$t('Status')"
              :items="translatedStatuses"
              item-text="status"
              item-value="value"
            ></v-select>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              v-model="accountData.tel_number"
              outlined
              dense
              :label="$t('Phone')"
              :rules="phoneRules"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6" v-if="userType == 'student'">
            <v-text-field
              v-model="accountData.student_identification"
              outlined
              dense
              :label="$t('student_id')"
            ></v-text-field>
          </v-col>
          <v-col v-else-if="userType == 'school' || (userType == 'guardian' && accountData.role_id==4)">
            <v-text-field
              v-model="accountData.balance"
              :disabled="userType == 'guardian' && accountData.role_id==4"
              outlined
              dense
              :label="$t('balance')"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <v-btn color="primary" class="me-3 mt-4" @click="saveUser">
              {{ $t("Save Changes") }}
            </v-btn>
            <v-btn
              color="secondary"
              outlined
              class="mt-4"
              @click="cancel"
            >
              {{ $t("cancel") }}
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>

import AvatarImageComponent from '../../../components/AvatarImageComponent.vue'
import { mdiAlertOutline, mdiCloudUploadOutline } from "@mdi/js";
import { adminProfileStore } from "@/utils/helpers";

export default {
  components: {
    AvatarImageComponent
  },
  setup() {
    return { adminProfileStore }
  },
  props: {
    accountData: {
      type: Object,
      default: () => {},
    },
    userType: {
      type: String,
      default: () => {},
    },
  },
  data() {
    return {
      valid: true,
      nameRules: [(v) => !!v || this.$t("name_is_required")],
      emailRules: [
        v => !!v || this.$t('email_is_required'),
        v => /.+@.+\..+/.test(v) || this.$t('email_must_be_valid'),
      ],
      phoneRules: [
        v => /^(|\d)+$/.test(v) || this.$t('phone_must_be_valid'),
      ],
      statuses: [
        { status: "active", value: 1 },
        //{ status: "pending", value: 2 },
        { status: "suspended", value: 3 },
      ],
      roles: [
        { role: "admin", value: 1},
        { role: "school", value: 2 },
        { role: "driver", value: 3 },
        { role: "parent", value: 4},
        { role: "guardian", value: 5},
        { role: "student", value: 6}
      ],
      icons: {
        mdiAlertOutline,
        mdiCloudUploadOutline,
      },
    };
  },
  computed: {
    translatedStatuses() {
      return this.statuses.map(status => ({
        ...status,
        status: this.$t(status.status)
      }));
    },
    translatedRoles() {
      return this.roles.map(role => ({
        ...role,
        role: this.$t(role.role)
      }));
    }
  },
  methods: {
    validate() {
      return this.$refs.form.validate();
    },
    saveUser() {
      if (!this.validate()) return;
      this.$emit("save-user-info");
    },
    cancel()
    {
      this.$router.go(-1);
    }
  },
};
</script>
