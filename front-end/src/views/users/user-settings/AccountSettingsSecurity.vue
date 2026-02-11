<template>
  <v-card
    flat
    class="mt-5"
  >
    <v-form ref="form">
      <div class="px-3">
        <v-card-text class="pt-5">
          <v-row>
            <v-col
              cols="12"
              sm="8"
              md="6"
            >
              <!-- new password -->
              <v-text-field
                v-model="newPassword"
                :rules="[rules.required, rules.min, rules.passwordMatch(newPassword, cPassword)]"
                :type="isNewPasswordVisible ? 'text' : 'password'"
                :append-icon="isNewPasswordVisible ? icons.mdiEyeOffOutline:icons.mdiEyeOutline"
                :label="$t('new_password')"
                outlined
                dense
                :hint="$t('password_hint')"
                persistent-hint
                @click:append="isNewPasswordVisible = !isNewPasswordVisible"
              ></v-text-field>

              <!-- confirm password -->
              <v-text-field
                v-model="cPassword"
                :rules="[rules.required]"
                :type="isCPasswordVisible ? 'text' : 'password'"
                :append-icon="isCPasswordVisible ? icons.mdiEyeOffOutline:icons.mdiEyeOutline"
                :label="$t('confirm_new_password')"
                outlined
                dense
                class="mt-3"
                @click:append="isCPasswordVisible = !isCPasswordVisible"
              ></v-text-field>
            </v-col>

            <v-col
              cols="12"
              sm="4"
              md="6"
              class="d-none d-sm-flex justify-center position-relative"
            >
              <v-img
                contain
                max-width="130"
                src="@/assets/images/3d-characters/pose-m-1.png"
                class="security-character"
              ></v-img>
            </v-col>
          </v-row>
        </v-card-text>
      </div>

      <div class="pa-3">
        <!-- action buttons -->
        <v-card-text>
          <v-btn
            color="primary"
            class="me-3 mt-3"
            @click="savePassword"
          >
            {{ $t('save_changes') }}
          </v-btn>
          <v-btn
            color="secondary"
            outlined
            class="mt-3"
            @click="cancel"
          >
            {{ $t('cancel') }}
          </v-btn>
        </v-card-text>
      </div>
    </v-form>
  </v-card>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import { mdiKeyOutline, mdiLockOpenOutline, mdiEyeOffOutline, mdiEyeOutline } from '@mdi/js'
import { ref } from '@vue/composition-api'

export default {
  setup() {
    const isNewPasswordVisible = ref(false)
    const isCPasswordVisible = ref(false)
    const newPassword = ref('87654321')
    const cPassword = ref('87654321')

    return {
      isNewPasswordVisible,
      isCPasswordVisible,
      newPassword,
      cPassword,
      icons: {
        mdiKeyOutline,
        mdiLockOpenOutline,
        mdiEyeOffOutline,
        mdiEyeOutline,
      },
      rules: {
        required: value => !!value || this.$t('required'),
        min: v => v.length >= 8 || this.$t('min_8_characters'),
        passwordMatch: (c, n) => c == n || this.$t('passwords_dont_match'),
      },
    }
  },
  methods: {
    validate() {
      return this.$refs.form.validate();
    },
    savePassword() {
      if (!this.validate()) return;
      this.$emit("save-user-password", this.newPassword);
    },
    cancel()
    {
      this.$router.go(-1);
    }
  },
}
</script>

<style lang="scss" scoped>
.two-factor-auth {
  max-width: 25rem;
}
.security-character {
  position: absolute;
  bottom: -0.5rem;
}
</style>
