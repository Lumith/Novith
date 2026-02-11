<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //next_stop_is_your_pickup_location
        EventType::updateOrCreate(
            ['notification_name' => 'next_stop_is_your_pickup_location_notification_on_off'],
            [
                'title' => 'Next Stop Is Your Pickup Location. Please Get Ready',
                'title_en' => 'Next Stop Is Your Pickup Location. Please Get Ready',
                'title_es' => 'La Próxima Parada Es Tu Ubicación de Recogida. Por Favor Prepárate',
                'title_fr' => 'Le Prochain Arrêt Est Votre Lieu de Ramassage. Veuillez Vous Préparer',
                'title_ar' => 'المحطة التالية هي موقع التقاطك. يرجى الاستعداد',
            ]
        );

        //bus_near_pickup_location_notification_by_distance
        EventType::updateOrCreate(
            ['notification_name' => 'bus_near_pickup_location_notification_by_distance'],
            [
                'title' => 'Bus Near Pickup Location',
                'title_en' => 'Bus Near Pickup Location',
                'title_es' => 'Autobus Cercano a la Ubicación de Recogida',
                'title_fr' => 'Autobus Près du Lieu de Ramassage',
                'title_ar' => 'الحافلة قريبة من موقع التقاط',
            ]
        );

        //bus_arrived_at_pickup_location
        EventType::updateOrCreate(
            ['notification_name' => 'bus_arrived_at_pickup_location_notification_on_off'],
            [
                'title' => 'Bus Arrived At Pickup Location',
                'title_en' => 'Bus Arrived At Pickup Location',
                'title_es' => 'Autobus Llegó a la Ubicación de Recogida',
                'title_fr' => 'Autobus Arrivé au Lieu de Ramassage',
                'title_ar' => 'وصلت الحافلة إلى موقع التقاط',
            ]
        );

        //student_is_picked_up
        EventType::updateOrCreate(
            ['notification_name' => 'student_is_picked_up_notification_on_off'],
            [
                'title' => 'Student Is Picked Up',
                'title_en' => 'Student Is Picked Up',
                'title_es' => 'Estudiante Fue Recogido',
                'title_fr' => 'Étudiant a Été Ramassé',
                'title_ar' => 'تم التقاط الطالب',
            ]
        );

        //student_is_missed_pickup
        EventType::updateOrCreate(
            ['notification_name' => 'student_is_missed_pickup_notification_on_off'],
            [
                'title' => 'Bus Left Pickup Location. Student Is Missed Pickup',
                'title_en' => 'Bus Left Pickup Location. Student Is Missed Pickup',
                'title_es' => 'Autobus Salió de la Ubicación de Recogida. Estudiante Perdió el Recogida',
                'title_fr' => 'Autobus a Quitté le Lieu de Ramassage. Étudiant a Manqué le Ramassage',
                'title_ar' => 'غادرت الحافلة موقع التقاط. الطالب فاته التقاط',
            ]
        );

        //bus_arrived_at_school
        EventType::updateOrCreate(
            ['notification_name' => 'bus_arrived_at_school_notification_on_off'],
            [
                'title' => 'Bus Arrived At School',
                'title_en' => 'Bus Arrived At School',
                'title_es' => 'Autobus Llegó a la Escuela',
                'title_fr' => 'Autobus Arrivé à l\'École',
                'title_ar' => 'وصلت الحافلة إلى المدرسة',
            ]
        );

        //bus_near_drop_off_location_notification_on_off
        EventType::updateOrCreate(
            ['notification_name' => 'bus_near_drop_off_location_notification_on_off'],
            [
                'title' => 'Bus Near DropOff Location',
                'title_en' => 'Bus Near DropOff Location',
                'title_es' => 'Autobus Cercano a la Ubicación de Descarga',
                'title_fr' => 'Autobus Près du Lieu de Dépôt',
                'title_ar' => 'الحافلة قريبة من موقع الإنزال',
            ]
        );

        //bus_arrived_at_drop_off_location
        EventType::updateOrCreate(
            ['notification_name' => 'bus_arrived_at_drop_off_location_notification_on_off'],
            [
                'title' => 'Bus Arrived At DropOff Location',
                'title_en' => 'Bus Arrived At DropOff Location',
                'title_es' => 'Autobus Llegó a la Ubicación de Descarga',
                'title_fr' => 'Autobus Arrivé au Lieu de Dépôt',
                'title_ar' => 'وصلت الحافلة إلى موقع الإنزال',
            ]
        );
    }
}
