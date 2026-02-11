<template>
  <v-fade-transition mode="out-in">
        <v-select
        v-model="selectedLang"
        :items="$getAvailableLanguages()"
        item-value="code"
        item-text="name"
        density="compact"
        hide-details
        style="max-width: 50px"
        class="mx-4"
        @change="changeLanguage"
        >
        <!-- Selected item (shows only flag) -->
        <template v-slot:selection="{ item }">
            <div class="d-flex align-center">
            <img
                :src="require(`@/assets/flags/${item.code}.png`)"
                alt=""
                width="22"
                height="22"
                style="border-radius: 3px;"
            />
            </div>
        </template>

        <!-- List items (flag + text) -->
        <template v-slot:item="{ item }">
            <div class="d-flex align-center">
            <img
                :src="require(`@/assets/flags/${item.code}.png`)"
                alt=""
                width="22"
                height="22"
                style="border-radius: 3px; margin-right: 8px;"
            />
            <span>{{ item.text }}</span>
            </div>
        </template>
        </v-select>
  </v-fade-transition>
</template>

<script>
import { mdiWeatherNight, mdiWeatherSunny } from '@mdi/js'

export default {
    data() {
        return {
        selectedLang: null,
        };
    },
    methods: {
        changeLanguage(lang) {
        this.$i18n.locale = lang;
        this.$setDocumentLangDir(lang);
        },
    },
    mounted() {
        this.selectedLang = this.$i18n.locale;
    }
}
</script>
