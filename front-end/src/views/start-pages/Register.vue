<template>
    <div class="auth-wrapper auth-v1">
        <!-- Language Selector -->
        <div class="language-selector-wrapper">
            <v-select
                v-model="selectedLang"
                :items="$getAvailableLanguages()"
                item-value="code"
                item-text="name"
                dense
                outlined
                hide-details
                class="language-selector"
                :menu-props="{
                    offsetY: true,
                    contentClass: 'language-menu',
                    transition: 'slide-y-transition'
                }"
                @change="changeLanguage"
            >
                <!-- Selected item (flag with subtle background) -->
                <template v-slot:selection="{ item }">
                    <div class="selected-language">
                        <div class="flag-container">
                            <img
                                :src="require(`@/assets/flags/${item.code}.png`)"
                                alt=""
                                class="flag-img"
                            />
                        </div>
                    </div>
                </template>

                <!-- Dropdown items (flag + text with hover effects) -->
                <template v-slot:item="{ item }">
                    <div class="language-item">
                        <div class="flag-container">
                            <img
                                :src="require(`@/assets/flags/${item.code}.png`)"
                                alt=""
                                class="flag-img"
                            />
                        </div>
                        <span class="language-name">{{ item.text }}</span>
                    </div>
                </template>
            </v-select>
        </div>

        <div class="auth-inner">
            <vue-element-loading :active="submiting" />
            <v-card class="auth-card">
                <!-- logo -->
                <v-card-title class="d-flex align-center justify-center py-7">
                    <v-img
                        :src="require('@/assets/images/logos/logo.png')"
                        max-height="30px"
                        max-width="30px"
                        alt="logo"
                        contain
                        class="me-3"
                    ></v-img>

                    <h2 class="text-2xl font-weight-semibold">
                        {{ systemName }}
                    </h2>
                </v-card-title>

                <!-- title -->
                <v-card-text>
                    <p class="font-weight-semibold text--primary mb-2">
                        {{ $t('create_new_school_account') }}
                    </p>
                    <p class="mb-2">
                        {{ $t('please_fill_form_to_create_school_account') }}
                    </p>
                </v-card-text>

                <!-- register form -->
                <v-card-text>
                    <v-form ref="form" v-model="valid" lazy-validation>
                        <v-text-field
                            v-model="username"
                            outlined
                            :label="$t('full_school_name')"
                            placeholder="JohnDoe"
                            required
                            :rules="userNameRules"
                            hide-details
                            class="mb-3"
                        ></v-text-field>

                        <v-text-field
                            v-model="email"
                            outlined
                            :label="$t('Email')"
                            placeholder="john@example.com"
                            hide-details
                            required
                            :rules="emailRules"
                            class="mb-3"
                        ></v-text-field>

                        <v-text-field
                            v-model="password"
                            outlined
                            :type="isPasswordVisible ? 'text' : 'password'"
                            :label="$t('Password')"
                            required
                            :rules="passRules"
                            placeholder="············"
                            :append-icon="
                                isPasswordVisible
                                    ? icons.mdiEyeOffOutline
                                    : icons.mdiEyeOutline
                            "
                            hide-details
                            @click:append="
                                isPasswordVisible = !isPasswordVisible
                            "
                        ></v-text-field>

                        <v-checkbox v-model="agree">
                            <template v-slot:label>
                                <div>
                                        {{ $t('i_agree_to') }}
                                    <v-tooltip location="bottom">
                                        <template v-slot:activator="{ props }">
                                            <a
                                                href="/terms"
                                                target="_blank"
                                                v-bind="props"
                                                @click.stop
                                            >
                                                {{ $t('Terms & Conditions') }}
                                            </a>
                                        </template>
                                    </v-tooltip>
                                </div>
                            </template>
                        </v-checkbox>

                        <v-card-text
                            class="d-flex align-center justify-center flex-wrap mt-2"
                        >
                            <span class="me-2"> {{ $t('need_help') }} </span>
                            <a href="#" @click="adminInfoDialog = true">
                                {{ $t('contact_us') }}
                            </a>
                        </v-card-text>

                        <VueSimpleRecaptcha
                            :sitekey="siteKey"
                            ref="recaptcha"
                            @callback="callback"
                            :invisible="true"
                        />
                        <v-btn
                            block
                            color="primary"
                            class="mt-6"
                            @click="register"
                            :disabled="!agree"
                        >
                            {{ $t('sign_up') }}
                        </v-btn>
                    </v-form>
                </v-card-text>

                <!-- create new account  -->
                <v-card-text
                    class="d-flex align-center justify-center flex-wrap mt-2"
                >
                    <span class="me-2"> {{ $t('already_have_account') }} </span>
                    <router-link :to="{ name: 'login' }">
                        {{ $t('sign_in_instead') }}
                    </router-link>
                </v-card-text>
            </v-card>
            <v-row justify="center">
                <v-dialog
                    v-model="adminInfoDialog"
                    persistent
                    max-width="600px"
                >
                    <v-card>
                        <v-card-title>
                            <span class="text-h5">{{ $t('contact_us') }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        {{ $t('Email') }}:
                                        <a
                                            v-bind="{
                                                href:
                                                    'mailto:' + adminInfo.email,
                                            }"
                                        >
                                            {{ adminInfo.email }}
                                        </a>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        {{ $t('Phone Number') }}:
                                        <a
                                            v-bind="{
                                                href: 'tel:' + adminInfo.phone,
                                            }"
                                        >
                                            {{ adminInfo.phone }}
                                        </a>
                                    </v-col>
                                    <v-col cols="12">
                                        {{ $t('Address') }}:
                                        <span>{{ adminInfo.address }}</span>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="adminInfoDialog = false"
                            >
                                {{ $t('close') }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-row>
        </div>

        <!-- background triangle shape  -->
        <img
            class="auth-mask-bg"
            height="190"
            :src="
                require(`@/assets/images/misc/mask-${
                    $vuetify.theme.dark ? 'dark' : 'light'
                }.png`)
            "
        />

        <!-- tree -->
        <v-img
            class="auth-tree my-5"
            width="247"
            height="185"
            src="@/assets/images/misc/school.png"
        ></v-img>

        <!-- tree  -->
        <v-img
            class="auth-tree-3"
            width="400"
            height="250"
            src="@/assets/images/misc/school-bus-side-view.png"
        ></v-img>
    </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import {
    mdiFacebook,
    mdiTwitter,
    mdiGithub,
    mdiGoogle,
    mdiEyeOutline,
    mdiEyeOffOutline,
} from "@mdi/js";
import { ref } from "@vue/composition-api";
import { Keys } from "/src/config.js";
import VueElementLoading from "vue-element-loading";
import AuthService from "@/services/AuthService";
import VueSimpleRecaptcha from "vue-simple-recaptcha";

export default {
    components: {
        VueElementLoading,
        VueSimpleRecaptcha,
    },
    setup() {
        const isPasswordVisible = ref(false);
        const username = ref("");
        const email = ref("");
        const password = ref("");
        const socialLink = [
            {
                icon: mdiFacebook,
                color: "#4267b2",
                colorInDark: "#4267b2",
            },
            {
                icon: mdiTwitter,
                color: "#1da1f2",
                colorInDark: "#1da1f2",
            },
            {
                icon: mdiGithub,
                color: "#272727",
                colorInDark: "#fff",
            },
            {
                icon: mdiGoogle,
                color: "#db4437",
                colorInDark: "#db4437",
            },
        ];

        return {
            systemName: Keys.VUE_APP_SYSTEM_NAME,
            siteKey: Keys.VUE_APP_RECAPTCHA_SITE_KEY,
            isPasswordVisible,
            username,
            email,
            password,
            socialLink,

            icons: {
                mdiEyeOutline,
                mdiEyeOffOutline,
            },
        };
    },
    data() {
        return {
            systemName: Keys.VUE_APP_SYSTEM_NAME,
            valid: true,
            submiting: false,
            selectedLang: null,
            emailRules: [
                (v) => !!v || this.$t("E-mail is required"),
                (v) => /.+@.+\..+/.test(v) || this.$t("E-mail must be valid"),
            ],
            passRules: [(v) => !!v || this.$t("Password is required")],
            userNameRules: [(v) => !!v || this.$t("username_required")],
            agree: false,
            adminInfo: {
                email: "",
                phone: "",
                address: "",
            },
            adminInfoDialog: false,
        };
    },
    mounted() {
        this.selectedLang = localStorage.getItem("lang") || this.$i18n.locale;
        this.$setDocumentLangDir(this.selectedLang);
        this.loadAdminInfo();
    },
    methods: {
        changeLanguage(lang) {
            this.$i18n.locale = lang;
            this.$setDocumentLangDir(lang);
            localStorage.setItem("lang", lang);
        },
        loadAdminInfo() {
            this.submiting = true;
            axios
                .get(`/users/admin-info`)
                .then((response) => {
                    console.log(response.data.admin_info.email);
                    this.adminInfo.email = response.data.admin_info.email;
                    this.adminInfo.phone = response.data.admin_info.phone;
                    this.adminInfo.address = response.data.admin_info.address;
                })
                .catch((error) => {
                    this.$notify({
                        title: "Error",
                        text: "Error while retrieving buses",
                        type: "error",
                    });
                    console.log(error);
                })
                .then(() => {
                    this.submiting = false;
                });
        },
        validate() {
            return this.$refs.form.validate();
        },
        async callback(token) {
            const payload = {
                email: this.email,
                name: this.username,
                password: this.password,
                recaptcha: token,
                notify: this.$notify,
            };
            this.error = null;
            try {
                this.submiting = true;
                const user = await AuthService.register(payload);
                console.log(user);
                this.submiting = false;
                if (user) {
                    const isAdministrator = user.role_id == 1;
                    const isSchoolAdmin = user.role_id == 2;
                    this.$router.push(
                        this.$router.currentRoute.query.to || isAdministrator
                            ? "/admin-dashboard"
                            : isSchoolAdmin
                            ? "/school-dashboard"
                            : "/error-404"
                    );
                }
            } catch (error) {
                console.log(error);
                this.submiting = false;
                //this.error = getError(error);
            }
        },
        async register() {
            if (!this.validate()) return;
            this.$refs.recaptcha.execute();
        },
    },
};
</script>

<style lang="scss" scoped>
@import '~@/plugins/vuetify/default-preset/preset/pages/auth.scss';

.language-selector-wrapper {
  position: absolute;
  top: 24px;
  right: 24px;
  z-index: 10;
}

.language-selector {
  max-width: 70px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);

  &:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-1px);
  }

  ::v-deep .v-input__control {
    min-height: 44px !important;
  }

  ::v-deep .v-input__slot {
    border: 2px solid transparent !important;
    border-radius: 12px !important;
    padding: 0 8px !important;
    min-height: 44px !important;
    background: transparent !important;
    transition: all 0.3s ease;

    &:hover {
      border-color: rgba(var(--v-primary-base), 0.3) !important;
    }
  }

  ::v-deep .v-select__selections {
    padding: 0 !important;
  }
}

.theme--dark {
  .language-selector {
    background: rgba(30, 30, 30, 0.95);

    ::v-deep .v-input__slot:hover {
      border-color: rgba(var(--v-primary-base), 0.5) !important;
    }
  }
}

.selected-language {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 4px 0;
}

.flag-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.02);
  transition: all 0.2s ease;

  &:hover {
    transform: scale(1.05);
    background: rgba(0, 0, 0, 0.04);
  }
}

.theme--dark .flag-container {
  background: rgba(255, 255, 255, 0.05);

  &:hover {
    background: rgba(255, 255, 255, 0.08);
  }
}

.flag-img {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  object-fit: cover;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.language-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  width: 100%;
  transition: all 0.2s ease;
  cursor: pointer;

  &:hover {
    background: rgba(var(--v-primary-base), 0.08);

    .flag-container {
      transform: scale(1.05);
    }

    .language-name {
      color: rgb(var(--v-primary-base));
      transform: translateX(2px);
    }
  }
}

.language-name {
  margin-left: 12px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
}

::v-deep .language-menu {
  border-radius: 12px !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
  margin-top: 8px !important;
  overflow: hidden;

  .v-list {
    padding: 4px !important;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px);
  }

  .v-list-item {
    border-radius: 8px !important;
    margin: 2px 0 !important;
    min-height: 44px !important;

    &::before {
      border-radius: 8px !important;
    }
  }
}

.theme--dark ::v-deep .language-menu .v-list {
  background: rgba(30, 30, 30, 0.98) !important;
}

// Smooth animations
.slide-y-transition-enter-active,
.slide-y-transition-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-y-transition-enter,
.slide-y-transition-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
