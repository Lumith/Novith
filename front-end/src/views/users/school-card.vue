<template>
        <v-card>
            <v-card-title>
                <v-icon color="primary"> mdi-information </v-icon>
                <span class="px-2">{{ $t('school_information') }}</span>
            </v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('students') }}</p>
                        <div class="mt-4">
                            <p>{{ school.student_count }}</p>
                        </div>
                    </v-col>
                    <!-- drivers -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('drivers') }}</p>
                        <div class="mt-4">
                            <p>{{ school.driver_count }}</p>
                        </div>
                    </v-col>
                    <!-- buses -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('Buses') }}</p>
                        <div class="mt-4">
                            <p>{{ school.bus_count }}</p>
                        </div>
                    </v-col>

                    <!-- stops -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('Stops') }}</p>
                        <div class="mt-4">
                            <p>{{ school.stop_count }}</p>
                        </div>
                    </v-col>

                    <!-- routes -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('Routes') }}</p>
                        <div class="mt-4">
                            <p>{{ school.route_count }}</p>
                        </div>
                    </v-col>

                    <!-- trips -->
                    <v-col cols="12" md="4">
                        <p class="font-weight-bold">{{ $t('Trips') }}</p>
                        <div class="mt-4">
                            <p>{{ school.trip_count }}</p>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
</template>

<script>
import AvatarImageComponent from "../../components/AvatarImageComponent.vue";

export default {
    name: "schoolCard",
    props: {
        school: {
            type: Object,
            required: true,
        },
    },
    components: {
        AvatarImageComponent,
    },
};
</script>
