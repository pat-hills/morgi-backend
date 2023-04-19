<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlpha3CodeToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('alpha_2_code')->after('alpha_3_code');
            $table->string('timezone')->after('dial');
        });

        $countries = [
            [1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93],
            [2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355],
            [3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213],
            [4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684],
            [5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376],
            [6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244],
            [7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264],
            [8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0],
            [9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268],
            [10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54],
            [11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374],
            [12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297],
            [13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61],
            [14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43],
            [15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994],
            [16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242],
            [17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973],
            [18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880],
            [19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246],
            [20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375],
            [21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32],
            [22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501],
            [23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229],
            [24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441],
            [25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975],
            [26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591],
            [27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387],
            [28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267],
            [29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0],
            [30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55],
            [31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246],
            [32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673],
            [33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359],
            [34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226],
            [35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257],
            [36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855],
            [37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237],
            [38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1],
            [39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238],
            [40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345],
            [41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236],
            [42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235],
            [43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56],
            [44, 'CN', 'CHINA', 'China', 'CHN', 156, 86],
            [45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61],
            [46, 'CC', 'COCOS [KEELING) ISLANDS', 'Cocos [Keeling) Islands', NULL, NULL, 672],
            [47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57],
            [48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269],
            [49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242],
            [50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242],
            [51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682],
            [52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506],
            [53, 'CI', 'COTE D"IVOIRE', 'Cote D"Ivoire', 'CIV', 384, 225],
            [54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385],
            [55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53],
            [56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357],
            [57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420],
            [58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45],
            [59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253],
            [60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767],
            [61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809],
            [62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593],
            [63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20],
            [64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503],
            [65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240],
            [66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291],
            [67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372],
            [68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251],
            [69, 'FK', 'FALKLAND ISLANDS [MALVINAS)', 'Falkland Islands [Malvinas)', 'FLK', 238, 500],
            [70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298],
            [71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679],
            [72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358],
            [73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33],
            [74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594],
            [75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689],
            [76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0],
            [77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241],
            [78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220],
            [79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995],
            [80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49],
            [81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233],
            [82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350],
            [83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30],
            [84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299],
            [85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473],
            [86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590],
            [87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671],
            [88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502],
            [89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224],
            [90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245],
            [91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592],
            [92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509],
            [93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0],
            [94, 'VA', 'HOLY SEE [VATICAN CITY STATE)', 'Holy See [Vatican City State)', 'VAT', 336, 39],
            [95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504],
            [96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852],
            [97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36],
            [98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354],
            [99, 'IN', 'INDIA', 'India', 'IND', 356, 91],
            [100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62],
            [101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98],
            [102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964],
            [103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353],
            [104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972],
            [105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39],
            [106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876],
            [107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81],
            [108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962],
            [109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7],
            [110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254],
            [111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686],
            [112, 'KP', 'KOREA, DEMOCRATIC PEOPLE"S REPUBLIC OF', 'Korea, Democratic People"s Republic of', 'PRK', 408, 850],
            [113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82],
            [114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965],
            [115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996],
            [116, 'LA', 'LAO PEOPLE"S DEMOCRATIC REPUBLIC', 'Lao People"s Democratic Republic', 'LAO', 418, 856],
            [117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371],
            [118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961],
            [119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266],
            [120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231],
            [121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218],
            [122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423],
            [123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370],
            [124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352],
            [125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853],
            [126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389],
            [127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261],
            [128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265],
            [129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60],
            [130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960],
            [131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223],
            [132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356],
            [133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692],
            [134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596],
            [135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222],
            [136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230],
            [137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269],
            [138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52],
            [139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691],
            [140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373],
            [141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377],
            [142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976],
            [143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664],
            [144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212],
            [145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258],
            [146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95],
            [147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264],
            [148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674],
            [149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977],
            [150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31],
            [151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599],
            [152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687],
            [153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64],
            [154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505],
            [155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227],
            [156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234],
            [157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683],
            [158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672],
            [159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670],
            [160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47],
            [161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968],
            [162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92],
            [163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680],
            [164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970],
            [165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507],
            [166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675],
            [167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595],
            [168, 'PE', 'PERU', 'Peru', 'PER', 604, 51],
            [169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63],
            [170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0],
            [171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48],
            [172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351],
            [173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787],
            [174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974],
            [175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262],
            [176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40],
            [177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70],
            [178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250],
            [179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290],
            [180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869],
            [181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758],
            [182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508],
            [183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784],
            [184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684],
            [185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378],
            [186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239],
            [187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966],
            [188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221],
            [189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381],
            [190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248],
            [191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232],
            [192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65],
            [193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421],
            [194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386],
            [195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677],
            [196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252],
            [197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27],
            [198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0],
            [199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34],
            [200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94],
            [201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249],
            [202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597],
            [203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47],
            [204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268],
            [205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46],
            [206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41],
            [207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963],
            [208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886],
            [209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992],
            [210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255],
            [211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66],
            [212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670],
            [213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228],
            [214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690],
            [215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676],
            [216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868],
            [217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216],
            [218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90],
            [219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370],
            [220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649],
            [221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688],
            [222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256],
            [223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380],
            [224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971],
            [225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44],
            [226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1],
            [227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1],
            [228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598],
            [229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998],
            [230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678],
            [231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58],
            [232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84],
            [233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284],
            [234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340],
            [235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681],
            [236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212],
            [237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967],
            [238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260],
            [239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263],
            [240, 'RS', 'SERBIA', 'Serbia', 'SRB', 688, 381],
            [241, 'AP', 'ASIA PACIFIC REGION', 'Asia / Pacific Region', '0', 0, 0],
            [242, 'ME', 'MONTENEGRO', 'Montenegro', 'MNE', 499, 382],
            [243, 'AX', 'ALAND ISLANDS', 'Aland Islands', 'ALA', 248, 358],
            [244, 'BQ', 'BONAIRE, SINT EUSTATIUS AND SABA', 'Bonaire, Sint Eustatius and Saba', 'BES', 535, 599],
            [245, 'CW', 'CURACAO', 'Curacao', 'CUW', 531, 599],
            [246, 'GG', 'GUERNSEY', 'Guernsey', 'GGY', 831, 44],
            [247, 'IM', 'ISLE OF MAN', 'Isle of Man', 'IMN', 833, 44],
            [248, 'JE', 'JERSEY', 'Jersey', 'JEY', 832, 44],
            [249, 'XK', 'KOSOVO', 'Kosovo', '---', 0, 381],
            [250, 'BL', 'SAINT BARTHELEMY', 'Saint Barthelemy', 'BLM', 652, 590],
            [251, 'MF', 'SAINT MARTIN', 'Saint Martin', 'MAF', 663, 590],
            [252, 'SX', 'SINT MAARTEN', 'Sint Maarten', 'SXM', 534, 1],
            [253, 'SS', 'SOUTH SUDAN', 'South Sudan', 'SSD', 728, 211],
            [253, 'AQ', 'SOUTH SUDAN', 'South Sudan', 'ATA', 728, 211],
            [253, 'BV', 'SOUTH SUDAN', 'South Sudan', 'BVT', 728, 211],
            [253, 'IO', 'SOUTH SUDAN', 'South Sudan', 'IOT', 728, 211],
            [253, 'CX', 'SOUTH SUDAN', 'South Sudan', 'CXR', 728, 211],
            [253, 'CC', 'SOUTH SUDAN', 'South Sudan', 'CCK', 728, 211],
            [253, 'TF', 'SOUTH SUDAN', 'South Sudan', 'ATF', 728, 211],
            [253, 'HM', 'SOUTH SUDAN', 'South Sudan', 'HMD', 728, 211],
            [253, 'RS', 'SOUTH SUDAN', 'South Sudan', 'XKX', 728, 211],
            [253, 'YT', 'SOUTH SUDAN', 'South Sudan', 'MYT', 728, 211],
            [253, 'PS', 'SOUTH SUDAN', 'South Sudan', 'PSE', 728, 211],
            [253, 'CS', 'SOUTH SUDAN', 'South Sudan', 'SCG', 728, 211],
            [253, 'GS', 'SOUTH SUDAN', 'South Sudan', 'SGS', 728, 211],
            [253, 'TL', 'SOUTH SUDAN', 'South Sudan', 'TLS', 728, 211],
            [253, 'UM', 'SOUTH SUDAN', 'South Sudan', 'UMI', 728, 211],
        ];

        //$country[4] is alpha_3_code, $country[1] is alpha_2_code
        foreach ($countries as $country){
            \App\Models\Country::query()->where('alpha_3_code', $country[4])->update(['alpha_2_code' => $country[1]]);
        }

        $timezones = [
            [1,'AD','Europe/Andorra'],
            [2,'AE','Asia/Dubai'],
            [3,'AF','Asia/Kabul'],
            [4,'AG','America/Antigua'],
            [5,'AI','America/Anguilla'],
            [6,'AL','Europe/Tirane'],
            [7,'AM','Asia/Yerevan'],
            [8,'AO','Africa/Luanda'],
            [9,'AQ','Antarctica/McMurdo'],
            [10,'AQ','Antarctica/Casey'],
            [11,'AQ','Antarctica/Davis'],
            [12,'AQ','Antarctica/DumontDUrville'],
            [13,'AQ','Antarctica/Mawson'],
            [14,'AQ','Antarctica/Palmer'],
            [15,'AQ','Antarctica/Rothera'],
            [16,'AQ','Antarctica/Syowa'],
            [17,'AQ','Antarctica/Troll'],
            [18,'AQ','Antarctica/Vostok'],
            [19,'AR','America/Argentina/Buenos_Aires'],
            [20,'AR','America/Argentina/Cordoba'],
            [21,'AR','America/Argentina/Salta'],
            [22,'AR','America/Argentina/Jujuy'],
            [23,'AR','America/Argentina/Tucuman'],
            [24,'AR','America/Argentina/Catamarca'],
            [25,'AR','America/Argentina/La_Rioja'],
            [26,'AR','America/Argentina/San_Juan'],
            [27,'AR','America/Argentina/Mendoza'],
            [28,'AR','America/Argentina/San_Luis'],
            [29,'AR','America/Argentina/Rio_Gallegos'],
            [30,'AR','America/Argentina/Ushuaia'],
            [31,'AS','Pacific/Pago_Pago'],
            [32,'AT','Europe/Vienna'],
            [33,'AU','Australia/Lord_Howe'],
            [34,'AU','Antarctica/Macquarie'],
            [35,'AU','Australia/Hobart'],
            [36,'AU','Australia/Currie'],
            [37,'AU','Australia/Melbourne'],
            [38,'AU','Australia/Sydney'],
            [39,'AU','Australia/Broken_Hill'],
            [40,'AU','Australia/Brisbane'],
            [41,'AU','Australia/Lindeman'],
            [42,'AU','Australia/Adelaide'],
            [43,'AU','Australia/Darwin'],
            [44,'AU','Australia/Perth'],
            [45,'AU','Australia/Eucla'],
            [46,'AW','America/Aruba'],
            [47,'AX','Europe/Mariehamn'],
            [48,'AZ','Asia/Baku'],
            [49,'BA','Europe/Sarajevo'],
            [50,'BB','America/Barbados'],
            [51,'BD','Asia/Dhaka'],
            [52,'BE','Europe/Brussels'],
            [53,'BF','Africa/Ouagadougou'],
            [54,'BG','Europe/Sofia'],
            [55,'BH','Asia/Bahrain'],
            [56,'BI','Africa/Bujumbura'],
            [57,'BJ','Africa/Porto-Novo'],
            [58,'BL','America/St_Barthelemy'],
            [59,'BM','Atlantic/Bermuda'],
            [60,'BN','Asia/Brunei'],
            [61,'BO','America/La_Paz'],
            [62,'BQ','America/Kralendijk'],
            [63,'BR','America/Noronha'],
            [64,'BR','America/Belem'],
            [65,'BR','America/Fortaleza'],
            [66,'BR','America/Recife'],
            [67,'BR','America/Araguaina'],
            [68,'BR','America/Maceio'],
            [69,'BR','America/Bahia'],
            [70,'BR','America/Sao_Paulo'],
            [71,'BR','America/Campo_Grande'],
            [72,'BR','America/Cuiaba'],
            [73,'BR','America/Santarem'],
            [74,'BR','America/Porto_Velho'],
            [75,'BR','America/Boa_Vista'],
            [76,'BR','America/Manaus'],
            [77,'BR','America/Eirunepe'],
            [78,'BR','America/Rio_Branco'],
            [79,'BS','America/Nassau'],
            [80,'BT','Asia/Thimphu'],
            [81,'BW','Africa/Gaborone'],
            [82,'BY','Europe/Minsk'],
            [83,'BZ','America/Belize'],
            [84,'CA','America/St_Johns'],
            [85,'CA','America/Halifax'],
            [86,'CA','America/Glace_Bay'],
            [87,'CA','America/Moncton'],
            [88,'CA','America/Goose_Bay'],
            [89,'CA','America/Blanc-Sablon'],
            [90,'CA','America/Toronto'],
            [91,'CA','America/Nipigon'],
            [92,'CA','America/Thunder_Bay'],
            [93,'CA','America/Iqaluit'],
            [94,'CA','America/Pangnirtung'],
            [95,'CA','America/Atikokan'],
            [96,'CA','America/Winnipeg'],
            [97,'CA','America/Rainy_River'],
            [98,'CA','America/Resolute'],
            [99,'CA','America/Rankin_Inlet'],
            [100,'CA','America/Regina'],
            [101,'CA','America/Swift_Current'],
            [102,'CA','America/Edmonton'],
            [103,'CA','America/Cambridge_Bay'],
            [104,'CA','America/Yellowknife'],
            [105,'CA','America/Inuvik'],
            [106,'CA','America/Creston'],
            [107,'CA','America/Dawson_Creek'],
            [108,'CA','America/Fort_Nelson'],
            [109,'CA','America/Vancouver'],
            [110,'CA','America/Whitehorse'],
            [111,'CA','America/Dawson'],
            [112,'CC','Indian/Cocos'],
            [113,'CD','Africa/Kinshasa'],
            [114,'CD','Africa/Lubumbashi'],
            [115,'CF','Africa/Bangui'],
            [116,'CG','Africa/Brazzaville'],
            [117,'CH','Europe/Zurich'],
            [118,'CI','Africa/Abidjan'],
            [119,'CK','Pacific/Rarotonga'],
            [120,'CL','America/Santiago'],
            [121,'CL','America/Punta_Arenas'],
            [122,'CL','Pacific/Easter'],
            [123,'CM','Africa/Douala'],
            [124,'CN','Asia/Shanghai'],
            [125,'CN','Asia/Urumqi'],
            [126,'CO','America/Bogota'],
            [127,'CR','America/Costa_Rica'],
            [128,'CU','America/Havana'],
            [129,'CV','Atlantic/Cape_Verde'],
            [130,'CW','America/Curacao'],
            [131,'CX','Indian/Christmas'],
            [132,'CY','Asia/Nicosia'],
            [133,'CY','Asia/Famagusta'],
            [134,'CZ','Europe/Prague'],
            [135,'DE','Europe/Berlin'],
            [136,'DE','Europe/Busingen'],
            [137,'DJ','Africa/Djibouti'],
            [138,'DK','Europe/Copenhagen'],
            [139,'DM','America/Dominica'],
            [140,'DO','America/Santo_Domingo'],
            [141,'DZ','Africa/Algiers'],
            [142,'EC','America/Guayaquil'],
            [143,'EC','Pacific/Galapagos'],
            [144,'EE','Europe/Tallinn'],
            [145,'EG','Africa/Cairo'],
            [146,'EH','Africa/El_Aaiun'],
            [147,'ER','Africa/Asmara'],
            [148,'ES','Europe/Madrid'],
            [149,'ES','Africa/Ceuta'],
            [150,'ES','Atlantic/Canary'],
            [151,'ET','Africa/Addis_Ababa'],
            [152,'FI','Europe/Helsinki'],
            [153,'FJ','Pacific/Fiji'],
            [154,'FK','Atlantic/Stanley'],
            [155,'FM','Pacific/Chuuk'],
            [156,'FM','Pacific/Pohnpei'],
            [157,'FM','Pacific/Kosrae'],
            [158,'FO','Atlantic/Faroe'],
            [159,'FR','Europe/Paris'],
            [160,'GA','Africa/Libreville'],
            [161,'GB','Europe/London'],
            [162,'GD','America/Grenada'],
            [163,'GE','Asia/Tbilisi'],
            [164,'GF','America/Cayenne'],
            [165,'GG','Europe/Guernsey'],
            [166,'GH','Africa/Accra'],
            [167,'GI','Europe/Gibraltar'],
            [168,'GL','America/Godthab'],
            [169,'GL','America/Danmarkshavn'],
            [170,'GL','America/Scoresbysund'],
            [171,'GL','America/Thule'],
            [172,'GM','Africa/Banjul'],
            [173,'GN','Africa/Conakry'],
            [174,'GP','America/Guadeloupe'],
            [175,'GQ','Africa/Malabo'],
            [176,'GR','Europe/Athens'],
            [177,'GS','Atlantic/South_Georgia'],
            [178,'GT','America/Guatemala'],
            [179,'GU','Pacific/Guam'],
            [180,'GW','Africa/Bissau'],
            [181,'GY','America/Guyana'],
            [182,'HK','Asia/Hong_Kong'],
            [183,'HN','America/Tegucigalpa'],
            [184,'HR','Europe/Zagreb'],
            [185,'HT','America/Port-au-Prince'],
            [186,'HU','Europe/Budapest'],
            [187,'ID','Asia/Jakarta'],
            [188,'ID','Asia/Pontianak'],
            [189,'ID','Asia/Makassar'],
            [190,'ID','Asia/Jayapura'],
            [191,'IE','Europe/Dublin'],
            [192,'IL','Asia/Jerusalem'],
            [193,'IM','Europe/Isle_of_Man'],
            [194,'IN','Asia/Kolkata'],
            [195,'IO','Indian/Chagos'],
            [196,'IQ','Asia/Baghdad'],
            [197,'IR','Asia/Tehran'],
            [198,'IS','Atlantic/Reykjavik'],
            [199,'IT','Europe/Rome'],
            [200,'JE','Europe/Jersey'],
            [201,'JM','America/Jamaica'],
            [202,'JO','Asia/Amman'],
            [203,'JP','Asia/Tokyo'],
            [204,'KE','Africa/Nairobi'],
            [205,'KG','Asia/Bishkek'],
            [206,'KH','Asia/Phnom_Penh'],
            [207,'KI','Pacific/Tarawa'],
            [208,'KI','Pacific/Enderbury'],
            [209,'KI','Pacific/Kiritimati'],
            [210,'KM','Indian/Comoro'],
            [211,'KN','America/St_Kitts'],
            [212,'KP','Asia/Pyongyang'],
            [213,'KR','Asia/Seoul'],
            [214,'KW','Asia/Kuwait'],
            [215,'KY','America/Cayman'],
            [216,'KZ','Asia/Almaty'],
            [217,'KZ','Asia/Qyzylorda'],
            [218,'KZ','Asia/Aqtobe'],
            [219,'KZ','Asia/Aqtau'],
            [220,'KZ','Asia/Atyrau'],
            [221,'KZ','Asia/Oral'],
            [222,'LA','Asia/Vientiane'],
            [223,'LB','Asia/Beirut'],
            [224,'LC','America/St_Lucia'],
            [225,'LI','Europe/Vaduz'],
            [226,'LK','Asia/Colombo'],
            [227,'LR','Africa/Monrovia'],
            [228,'LS','Africa/Maseru'],
            [229,'LT','Europe/Vilnius'],
            [230,'LU','Europe/Luxembourg'],
            [231,'LV','Europe/Riga'],
            [232,'LY','Africa/Tripoli'],
            [233,'MA','Africa/Casablanca'],
            [234,'MC','Europe/Monaco'],
            [235,'MD','Europe/Chisinau'],
            [236,'ME','Europe/Podgorica'],
            [237,'MF','America/Marigot'],
            [238,'MG','Indian/Antananarivo'],
            [239,'MH','Pacific/Majuro'],
            [240,'MH','Pacific/Kwajalein'],
            [241,'MK','Europe/Skopje'],
            [242,'ML','Africa/Bamako'],
            [243,'MM','Asia/Yangon'],
            [244,'MN','Asia/Ulaanbaatar'],
            [245,'MN','Asia/Hovd'],
            [246,'MN','Asia/Choibalsan'],
            [247,'MO','Asia/Macau'],
            [248,'MP','Pacific/Saipan'],
            [249,'MQ','America/Martinique'],
            [250,'MR','Africa/Nouakchott'],
            [251,'MS','America/Montserrat'],
            [252,'MT','Europe/Malta'],
            [253,'MU','Indian/Mauritius'],
            [254,'MV','Indian/Maldives'],
            [255,'MW','Africa/Blantyre'],
            [256,'MX','America/Mexico_City'],
            [257,'MX','America/Cancun'],
            [258,'MX','America/Merida'],
            [259,'MX','America/Monterrey'],
            [260,'MX','America/Matamoros'],
            [261,'MX','America/Mazatlan'],
            [262,'MX','America/Chihuahua'],
            [263,'MX','America/Ojinaga'],
            [264,'MX','America/Hermosillo'],
            [265,'MX','America/Tijuana'],
            [266,'MX','America/Bahia_Banderas'],
            [267,'MY','Asia/Kuala_Lumpur'],
            [268,'MY','Asia/Kuching'],
            [269,'MZ','Africa/Maputo'],
            [270,'NA','Africa/Windhoek'],
            [271,'NC','Pacific/Noumea'],
            [272,'NE','Africa/Niamey'],
            [273,'NF','Pacific/Norfolk'],
            [274,'NG','Africa/Lagos'],
            [275,'NI','America/Managua'],
            [276,'NL','Europe/Amsterdam'],
            [277,'NO','Europe/Oslo'],
            [278,'NP','Asia/Kathmandu'],
            [279,'NR','Pacific/Nauru'],
            [280,'NU','Pacific/Niue'],
            [281,'NZ','Pacific/Auckland'],
            [282,'NZ','Pacific/Chatham'],
            [283,'OM','Asia/Muscat'],
            [284,'PA','America/Panama'],
            [285,'PE','America/Lima'],
            [286,'PF','Pacific/Tahiti'],
            [287,'PF','Pacific/Marquesas'],
            [288,'PF','Pacific/Gambier'],
            [289,'PG','Pacific/Port_Moresby'],
            [290,'PG','Pacific/Bougainville'],
            [291,'PH','Asia/Manila'],
            [292,'PK','Asia/Karachi'],
            [293,'PL','Europe/Warsaw'],
            [294,'PM','America/Miquelon'],
            [295,'PN','Pacific/Pitcairn'],
            [296,'PR','America/Puerto_Rico'],
            [297,'PS','Asia/Gaza'],
            [298,'PS','Asia/Hebron'],
            [299,'PT','Europe/Lisbon'],
            [300,'PT','Atlantic/Madeira'],
            [301,'PT','Atlantic/Azores'],
            [302,'PW','Pacific/Palau'],
            [303,'PY','America/Asuncion'],
            [304,'QA','Asia/Qatar'],
            [305,'RE','Indian/Reunion'],
            [306,'RO','Europe/Bucharest'],
            [307,'RS','Europe/Belgrade'],
            [308,'RU','Europe/Kaliningrad'],
            [309,'RU','Europe/Moscow'],
            [310,'RU','Europe/Simferopol'],
            [311,'RU','Europe/Volgograd'],
            [312,'RU','Europe/Kirov'],
            [313,'RU','Europe/Astrakhan'],
            [314,'RU','Europe/Saratov'],
            [315,'RU','Europe/Ulyanovsk'],
            [316,'RU','Europe/Samara'],
            [317,'RU','Asia/Yekaterinburg'],
            [318,'RU','Asia/Omsk'],
            [319,'RU','Asia/Novosibirsk'],
            [320,'RU','Asia/Barnaul'],
            [321,'RU','Asia/Tomsk'],
            [322,'RU','Asia/Novokuznetsk'],
            [323,'RU','Asia/Krasnoyarsk'],
            [324,'RU','Asia/Irkutsk'],
            [325,'RU','Asia/Chita'],
            [326,'RU','Asia/Yakutsk'],
            [327,'RU','Asia/Khandyga'],
            [328,'RU','Asia/Vladivostok'],
            [329,'RU','Asia/Ust-Nera'],
            [330,'RU','Asia/Magadan'],
            [331,'RU','Asia/Sakhalin'],
            [332,'RU','Asia/Srednekolymsk'],
            [333,'RU','Asia/Kamchatka'],
            [334,'RU','Asia/Anadyr'],
            [335,'RW','Africa/Kigali'],
            [336,'SA','Asia/Riyadh'],
            [337,'SB','Pacific/Guadalcanal'],
            [338,'SC','Indian/Mahe'],
            [339,'SD','Africa/Khartoum'],
            [340,'SE','Europe/Stockholm'],
            [341,'SG','Asia/Singapore'],
            [342,'SH','Atlantic/St_Helena'],
            [343,'SI','Europe/Ljubljana'],
            [344,'SJ','Arctic/Longyearbyen'],
            [345,'SK','Europe/Bratislava'],
            [346,'SL','Africa/Freetown'],
            [347,'SM','Europe/San_Marino'],
            [348,'SN','Africa/Dakar'],
            [349,'SO','Africa/Mogadishu'],
            [350,'SR','America/Paramaribo'],
            [351,'SS','Africa/Juba'],
            [352,'ST','Africa/Sao_Tome'],
            [353,'SV','America/El_Salvador'],
            [354,'SX','America/Lower_Princes'],
            [355,'SY','Asia/Damascus'],
            [356,'SZ','Africa/Mbabane'],
            [357,'TC','America/Grand_Turk'],
            [358,'TD','Africa/Ndjamena'],
            [359,'TF','Indian/Kerguelen'],
            [360,'TG','Africa/Lome'],
            [361,'TH','Asia/Bangkok'],
            [362,'TJ','Asia/Dushanbe'],
            [363,'TK','Pacific/Fakaofo'],
            [364,'TL','Asia/Dili'],
            [365,'TM','Asia/Ashgabat'],
            [366,'TN','Africa/Tunis'],
            [367,'TO','Pacific/Tongatapu'],
            [368,'TR','Europe/Istanbul'],
            [369,'TT','America/Port_of_Spain'],
            [370,'TV','Pacific/Funafuti'],
            [371,'TW','Asia/Taipei'],
            [372,'TZ','Africa/Dar_es_Salaam'],
            [373,'UA','Europe/Kiev'],
            [374,'UA','Europe/Uzhgorod'],
            [375,'UA','Europe/Zaporozhye'],
            [376,'UG','Africa/Kampala'],
            [377,'UM','Pacific/Midway'],
            [378,'UM','Pacific/Wake'],
            [379,'US','America/New_York'],
            [380,'US','America/Detroit'],
            [381,'US','America/Kentucky/Louisville'],
            [382,'US','America/Kentucky/Monticello'],
            [383,'US','America/Indiana/Indianapolis'],
            [384,'US','America/Indiana/Vincennes'],
            [385,'US','America/Indiana/Winamac'],
            [386,'US','America/Indiana/Marengo'],
            [387,'US','America/Indiana/Petersburg'],
            [388,'US','America/Indiana/Vevay'],
            [389,'US','America/Chicago'],
            [390,'US','America/Indiana/Tell_City'],
            [391,'US','America/Indiana/Knox'],
            [392,'US','America/Menominee'],
            [393,'US','America/North_Dakota/Center'],
            [394,'US','America/North_Dakota/New_Salem'],
            [395,'US','America/North_Dakota/Beulah'],
            [396,'US','America/Denver'],
            [397,'US','America/Boise'],
            [398,'US','America/Phoenix'],
            [399,'US','America/Los_Angeles'],
            [400,'US','America/Anchorage'],
            [401,'US','America/Juneau'],
            [402,'US','America/Sitka'],
            [403,'US','America/Metlakatla'],
            [404,'US','America/Yakutat'],
            [405,'US','America/Nome'],
            [406,'US','America/Adak'],
            [407,'US','Pacific/Honolulu'],
            [408,'UY','America/Montevideo'],
            [409,'UZ','Asia/Samarkand'],
            [410,'UZ','Asia/Tashkent'],
            [411,'VA','Europe/Vatican'],
            [412,'VC','America/St_Vincent'],
            [413,'VE','America/Caracas'],
            [414,'VG','America/Tortola'],
            [415,'VI','America/St_Thomas'],
            [416,'VN','Asia/Ho_Chi_Minh'],
            [417,'VU','Pacific/Efate'],
            [418,'WF','Pacific/Wallis'],
            [419,'WS','Pacific/Apia'],
            [420,'YE','Asia/Aden'],
            [421,'YT','Indian/Mayotte'],
            [422,'ZA','Africa/Johannesburg'],
            [423,'ZM','Africa/Lusaka'],
            [424,'ZW','Africa/Harare'],
            [425,'BV', 'Europe/Rome'],
            [425,'CS', 'Europe/Rome'],
            [425,'AN', 'Pacific/Port_Moresby'],
            [425,'HM', 'Indian/Chagos']
        ];

        foreach ($timezones as $timezone){
            \App\Models\Country::query()->where('alpha_2_code', $timezone[1])->update(['timezone' => $timezone[2]]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('alpha_2_code');
            $table->dropColumn('timezone');
        });
    }
}