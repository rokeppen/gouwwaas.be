<?php

/*
|--------------------------------------------------------------------------
| Gouw Waas: districts and groups
|--------------------------------------------------------------------------
|
| Source: Scoutnet address list (scoutnet.be/adressen), October 2026.
| Each group: [name, municipality, group number, website or null].
| Websites were checked to load in October 2026; null = no working site known.
|
*/

return [

    'districts' => [

        'klaas' => [
            'name' => 'Klaas',
            'groups' => [
                ['Jin Sint-Niklaas', 'Sint-Niklaas', 'O3114G', null],
                ['Kriko-m', 'Sint-Niklaas', 'O3108G', 'https://www.kriko-m.be/'],
                ['Kristus Koning', 'Sint-Niklaas', 'O3101G', 'https://www.kriko-m.be/'],
                ['Mgr. Bermijn', 'Sint-Pauwels', 'O3111G', 'https://www.bermijn.net/'],
                ['Sint-Johan', 'Sint-Niklaas', 'O3112G', 'https://akabesintjohan.be/'],
                ['Sint-Joris', 'Sint-Niklaas', 'O3102S', null],
                ['Sint-Jozef', 'Sint-Niklaas', 'O3103G', 'https://www.tereken.be/'],
                ['Sint-Kristoffel', 'Sint-Niklaas', 'O3104S', 'https://www.sint-kristoffel.be/'],
                ['Sint-Martinus', 'Nieuwkerken-Waas', 'O3109S', 'https://www.scoutsnieuwkerken.be/'],
                ['Sint-Paulus en Sint-Franciscus', 'Sint-Niklaas', 'O3107G', null],
                ['Sint-Tarsicius', 'Sint-Niklaas', 'O3105S', 'https://www.sint-tarsicius.be/'],
                ['Sint-Vincentius', 'Sint-Niklaas', 'O3113M', 'https://www.vincies.be/'],
                ['Zeescouts Reynaert', 'Sint-Niklaas', 'O3106S', 'https://dobo.be/'],
            ],
        ],

        'lelie' => [
            'name' => 'Lelie',
            'groups' => [
                ['Gidsen St.-Martinus', 'Beveren-Waas', 'O3312M', 'https://www.scoutsbeveren.be/'],
                ['Sint-Jan', 'Steendorp', 'O3306G', null],
                ['Sint-Jan Berchmans', 'Rupelmonde', 'O3307S', 'https://scoutsrupelmonde.be/'],
                ['Sint-Johannes', 'Sint-Gillis-Waas', 'O3314G', 'https://www.scoutssintjohannes.be/'],
                ['Sint-Michiel', 'Kieldrecht', 'O3301G', 'https://www.scoutskieldrecht.be/'],
                ['Sint-Raphael', 'Beveren-Waas', 'O3302G', null],
                ['Sint-Willibrordus', 'Temse', 'O3305S', 'https://scoutstemse.be/'],
            ],
        ],

        'durme' => [
            'name' => 'Durme',
            'groups' => [
                ['Edelweiss', 'Lokeren', 'O3210M', 'https://www.gidsenedelweiss.be/'],
                ['O.L.V. Ter Heide', 'Waasmunster', 'O3205G', 'https://www.scoutswaasmunster.be/'],
                ['Scouts Jan Berchmans', 'Lokeren', 'O3202S', 'https://scoutsheirbrug.be/'],
                ['Scouts Thomas', 'Stekene', 'O3214G', 'https://www.scoutsklein-sinaai.be/'],
                ['Sint-Antonius', 'Moerbeke-Waas', 'O3204G', 'https://www.scoutsmoerbekewaas.be/'],
                ['Sint-Franciscus', 'Sinaai-Waas', 'O3207G', 'https://scoutssinaai.be/'],
                ['Sint-Laurentius', 'Lokeren', 'O3201S', 'https://www.scoutshoogland.be/'],
                ['Sint-Pieter', 'Hamme', 'O3203G', 'https://www.scoutshamme.be/'],
                ['Sint-Rochus', 'Waasmunster', 'O3206G', 'https://www.scoutssombeke.be/'],
            ],
        ],

        'land-van-aymon' => [
            'name' => 'Land van Aymon',
            'groups' => [
                ['Justus De Harduyn', 'Oudegem', 'O3407G', 'https://www.scoutsoudegem.be/'],
                ['Scouts en Gidsen Buggenhout', 'Buggenhout', 'O3405G', null],
                ['Sint-Ignatius', 'Zele', 'O3408S', 'https://scoutingzele.be/'],
                ['Sint-Paulus Baden Pauwel', 'Dendermonde', 'O3411G', 'https://www.scoutsdendermonde.be/'],
                ['Sint-Ursmarus', 'Baasrode', 'O3404S', 'https://www.scoutsbaasrode.be/'],
                ['Tijl Uilenspiegel', 'Lebbeke', 'O3401G', 'https://www.scoutslebbeke.be/'],
            ],
        ],

    ],

];
