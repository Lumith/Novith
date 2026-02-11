<template>
  <div id="misc">
    <img
      class="misc-mask"
      height="226"
      :src="require(`@/assets/images/misc/misc-mask-${$vuetify.theme.dark ? 'dark' : 'light'}.png`)"
    />

    <!-- Beautiful Language Selector -->
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

    <div class="page-title text-center px-5">
      <h2 class="text-2xl font-weight-semibold text--primary d-flex align-center justify-center">
        <span class="me-2">{{ $t('welcome_to_system', { systemName: systemName }) }}</span>
      </h2>
      <div v-if="!isUserLoggedIn" class="my-6">
        <v-btn
          color="primary"
          to="/login"
          class="mb-4 mx-4"
        >
          {{ $t('Login') }}
        </v-btn>

        <v-btn
          color="primary"
          to="/register"
          class="mb-4"
        >
          {{ $t('school_signup') }}
        </v-btn>
      </div>
      <div v-else class="my-6">
        <v-btn
          v-if="userRole === 'admin'"
          color="primary"
          to="/admin-dashboard"
          class="mb-4 mx-4"
        >
          {{ $t('dashboard') }}
        </v-btn>
        <v-btn
          v-else
          color="primary"
          to="/school-dashboard"
          class="mb-4 mx-4"
        >
          {{ $t('dashboard') }}
        </v-btn>
      </div>
      <div class="misc-character d-flex justify-center">
        <v-img
          max-width="700"
          src="@/assets/images/misc/home.png"
        ></v-img>
      </div>
    </div>
  </div>
</template>

<script>
import { mdiAlert } from '@mdi/js'
import {Keys} from '/src/config.js'
import auth from '@/services/AuthService'

export default {
  components: {
    Keys
  },
  setup() {
    return {
      icons: {
        mdiAlert,
      },
      systemName: Keys.VUE_APP_SYSTEM_NAME
    }
  },
  data() {
    return {
      isUserLoggedIn: false,
      userRole: null,
      selectedLang: null
    }
  },
  mounted() {
    this.isUserLoggedIn = auth.isUserLoggedIn()
    this.userRole = auth.getLoggedInUserRole()
    this.selectedLang = localStorage.getItem("lang") || this.$i18n.locale
    this.$setDocumentLangDir(this.selectedLang)
  },
  methods: {
    getError(message) {
      return this.$t(message)
    },
    changeLanguage(lang) {
      this.$i18n.locale = lang
      this.$setDocumentLangDir(lang)
      localStorage.setItem("lang", lang)
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~@/plugins/vuetify/default-preset/preset/misc.scss';

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
