<?php

use App\Models\Country;
use App\Models\Gender;
use App\Models\MicromorgiPackage;
use App\Models\PaymentPlatform;
use App\Models\Region;
use App\Models\TransactionType;
use App\Models\UserABGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseSeederMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Country::truncate();

        $countries = [
            [1,'Afghanistan','AFG',93],
            [2,'Aland Islands','ALA',358],
            [3,'Albania','ALB',355],
            [4,'Algeria','DZA',213],
            [5,'American Samoa','ASM',1684],
            [6,'Andorra','AND',376],
            [7,'Angola','AGO',244],
            [8,'Anguilla','AIA',1264],
            [9,'Antarctica','ATA',672],
            [10,'Antigua and Barbuda','ATG',1268],
            [11,'Argentina','ARG',54],
            [12,'Armenia','ARM',374],
            [13,'Aruba','ABW',297],
            [14,'Australia','AUS',61],
            [15,'Austria','AUT',43],
            [16,'Azerbaijan','AZE',994],
            [17,'Bahamas','BHS',1242],
            [18,'Bahrain','BHR',973],
            [19,'Bangladesh','BGD',880],
            [20,'Barbados','BRB',1246],
            [21,'Belarus','BLR',375],
            [22,'Belgium','BEL',32],
            [23,'Belize','BLZ',501],
            [24,'Benin','BEN',229],
            [25,'Bermuda','BMU',1441],
            [26,'Bhutan','BTN',975],
            [27,'Bolivia','BOL',591],
            [28,'Bonaire, Sint Eustatius and Saba','BES',599],
            [29,'Bosnia and Herzegovina','BIH',387],
            [30,'Botswana','BWA',267],
            [31,'Bouvet Island','BVT',55],
            [32,'Brazil','BRA',55],
            [33,'British Indian Ocean Territory','IOT',246],
            [34,'Brunei Darussalam','BRN',673],
            [35,'Bulgaria','BGR',359],
            [36,'Burkina Faso','BFA',226],
            [37,'Burundi','BDI',257],
            [38,'Cambodia','KHM',855],
            [39,'Cameroon','CMR',237],
            [40,'Canada','CAN',1],
            [41,'Cape Verde','CPV',238],
            [42,'Cayman Islands','CYM',1345],
            [43,'Central African Republic','CAF',236],
            [44,'Chad','TCD',235],
            [45,'Chile','CHL',56],
            [46,'China','CHN',86],
            [47,'Christmas Island','CXR',61],
            [48,'Cocos [Keeling] Islands','CCK',672],
            [49,'Colombia','COL',57],
            [50,'Comoros','COM',269],
            [51,'Congo','COG',242],
            [52,'Congo, Democratic Republic of the Congo','COD',242],
            [53,'Cook Islands','COK',682],
            [54,'Costa Rica','CRI',506],
            [55,'Cote D\'Ivoire','CIV',225],
            [56,'Croatia','HRV',385],
            [57,'Cuba','CUB',53],
            [58,'Curacao','CUW',599],
            [59,'Cyprus','CYP',357],
            [60,'Czech Republic','CZE',420],
            [61,'Denmark','DNK',45],
            [62,'Djibouti','DJI',253],
            [63,'Dominica','DMA',1767],
            [64,'Dominican Republic','DOM',1809],
            [65,'Ecuador','ECU',593],
            [66,'Egypt','EGY',20],
            [67,'El Salvador','SLV',503],
            [68,'Equatorial Guinea','GNQ',240],
            [69,'Eritrea','ERI',291],
            [70,'Estonia','EST',372],
            [71,'Ethiopia','ETH',251],
            [72,'Falkland Islands [Malvinas]','FLK',500],
            [73,'Faroe Islands','FRO',298],
            [74,'Fiji','FJI',679],
            [75,'Finland','FIN',358],
            [76,'France','FRA',33],
            [77,'French Guiana','GUF',594],
            [78,'French Polynesia','PYF',689],
            [79,'French Southern Territories','ATF',262],
            [80,'Gabon','GAB',241],
            [81,'Gambia','GMB',220],
            [82,'Georgia','GEO',995],
            [83,'Germany','DEU',49],
            [84,'Ghana','GHA',233],
            [85,'Gibraltar','GIB',350],
            [86,'Greece','GRC',30],
            [87,'Greenland','GRL',299],
            [88,'Grenada','GRD',1473],
            [89,'Guadeloupe','GLP',590],
            [90,'Guam','GUM',1671],
            [91,'Guatemala','GTM',502],
            [92,'Guernsey','GGY',44],
            [93,'Guinea','GIN',224],
            [94,'Guinea-Bissau','GNB',245],
            [95,'Guyana','GUY',592],
            [96,'Haiti','HTI',509],
            [97,'Heard Island and Mcdonald Islands','HMD',0],
            [98,'Holy See [Vatican City State]','VAT',39],
            [99,'Honduras','HND',504],
            [100,'Hong Kong','HKG',852],
            [101,'Hungary','HUN',36],
            [102,'Iceland','ISL',354],
            [103,'India','IND',91],
            [104,'Indonesia','IDN',62],
            [105,'Iran, Islamic Republic of','IRN',98],
            [106,'Iraq','IRQ',964],
            [107,'Ireland','IRL',353],
            [108,'Isle of Man','IMN',44],
            [109,'Israel','ISR',972],
            [110,'Italy','ITA',39],
            [111,'Jamaica','JAM',1876],
            [112,'Japan','JPN',81],
            [113,'Jersey','JEY',44],
            [114,'Jordan','JOR',962],
            [115,'Kazakhstan','KAZ',7],
            [116,'Kenya','KEN',254],
            [117,'Kiribati','KIR',686],
            [118,'Korea, Democratic People\'s Republic of','PRK',850],
            [119,'Korea, Republic of','KOR',82],
            [120,'Kosovo','XKX',381],
            [121,'Kuwait','KWT',965],
            [122,'Kyrgyzstan','KGZ',996],
            [123,'Lao People\'s Democratic Republic','LAO',856],
            [124,'Latvia','LVA',371],
            [125,'Lebanon','LBN',961],
            [126,'Lesotho','LSO',266],
            [127,'Liberia','LBR',231],
            [128,'Libyan Arab Jamahiriya','LBY',218],
            [129,'Liechtenstein','LIE',423],
            [130,'Lithuania','LTU',370],
            [131,'Luxembourg','LUX',352],
            [132,'Macao','MAC',853],
            [133,'Macedonia, the Former Yugoslav Republic of','MKD',389],
            [134,'Madagascar','MDG',261],
            [135,'Malawi','MWI',265],
            [136,'Malaysia','MYS',60],
            [137,'Maldives','MDV',960],
            [138,'Mali','MLI',223],
            [139,'Malta','MLT',356],
            [140,'Marshall Islands','MHL',692],
            [141,'Martinique','MTQ',596],
            [142,'Mauritania','MRT',222],
            [143,'Mauritius','MUS',230],
            [144,'Mayotte','MYT',269],
            [145,'Mexico','MEX',52],
            [146,'Micronesia, Federated States of','FSM',691],
            [147,'Moldova, Republic of','MDA',373],
            [148,'Monaco','MCO',377],
            [149,'Mongolia','MNG',976],
            [150,'Montenegro','MNE',382],
            [151,'Montserrat','MSR',1664],
            [152,'Morocco','MAR',212],
            [153,'Mozambique','MOZ',258],
            [154,'Myanmar','MMR',95],
            [155,'Namibia','NAM',264],
            [156,'Nauru','NRU',674],
            [157,'Nepal','NPL',977],
            [158,'Netherlands','NLD',31],
            [159,'Netherlands Antilles','ANT',599],
            [160,'New Caledonia','NCL',687],
            [161,'New Zealand','NZL',64],
            [162,'Nicaragua','NIC',505],
            [163,'Niger','NER',227],
            [164,'Nigeria','NGA',234],
            [165,'Niue','NIU',683],
            [166,'Norfolk Island','NFK',672],
            [167,'Northern Mariana Islands','MNP',1670],
            [168,'Norway','NOR',47],
            [169,'Oman','OMN',968],
            [170,'Pakistan','PAK',92],
            [171,'Palau','PLW',680],
            [172,'Palestinian Territory, Occupied','PSE',970],
            [173,'Panama','PAN',507],
            [174,'Papua New Guinea','PNG',675],
            [175,'Paraguay','PRY',595],
            [176,'Peru','PER',51],
            [177,'Philippines','PHL',63],
            [178,'Pitcairn','PCN',64],
            [179,'Poland','POL',48],
            [180,'Portugal','PRT',351],
            [181,'Puerto Rico','PRI',1787],
            [182,'Qatar','QAT',974],
            [183,'Reunion','REU',262],
            [184,'Romania','ROM',40],
            [185,'Russian Federation','RUS',70],
            [186,'Rwanda','RWA',250],
            [187,'Saint Barthelemy','BLM',590],
            [188,'Saint Helena','SHN',290],
            [189,'Saint Kitts and Nevis','KNA',1869],
            [190,'Saint Lucia','LCA',1758],
            [191,'Saint Martin','MAF',590],
            [192,'Saint Pierre and Miquelon','SPM',508],
            [193,'Saint Vincent and the Grenadines','VCT',1784],
            [194,'Samoa','WSM',684],
            [195,'San Marino','SMR',378],
            [196,'Sao Tome and Principe','STP',239],
            [197,'Saudi Arabia','SAU',966],
            [198,'Senegal','SEN',221],
            [199,'Serbia','SRB',381],
            [200,'Serbia and Montenegro','SCG',381],
            [201,'Seychelles','SYC',248],
            [202,'Sierra Leone','SLE',232],
            [203,'Singapore','SGP',65],
            [204,'Sint Maarten','SXM',1],
            [205,'Slovakia','SVK',421],
            [206,'Slovenia','SVN',386],
            [207,'Solomon Islands','SLB',677],
            [208,'Somalia','SOM',252],
            [209,'South Africa','ZAF',27],
            [210,'South Georgia and the South Sandwich Islands','SGS',500],
            [211,'South Sudan','SSD',211],
            [212,'Spain','ESP',34],
            [213,'Sri Lanka','LKA',94],
            [214,'Sudan','SDN',249],
            [215,'Suriname','SUR',597],
            [216,'Svalbard and Jan Mayen','SJM',47],
            [217,'Swaziland','SWZ',268],
            [218,'Sweden','SWE',46],
            [219,'Switzerland','CHE',41],
            [220,'Syrian Arab Republic','SYR',963],
            [221,'Taiwan, Province of China','TWN',886],
            [222,'Tajikistan','TJK',992],
            [223,'Tanzania, United Republic of','TZA',255],
            [224,'Thailand','THA',66],
            [225,'Timor-Leste','TLS',670],
            [226,'Togo','TGO',228],
            [227,'Tokelau','TKL',690],
            [228,'Tonga','TON',676],
            [229,'Trinidad and Tobago','TTO',1868],
            [230,'Tunisia','TUN',216],
            [231,'Turkey','TUR',90],
            [232,'Turkmenistan','TKM',7370],
            [233,'Turks and Caicos Islands','TCA',1649],
            [234,'Tuvalu','TUV',688],
            [235,'Uganda','UGA',256],
            [236,'Ukraine','UKR',380],
            [237,'United Arab Emirates','ARE',971],
            [238,'United Kingdom','GBR',44],
            [239,'United States','USA',1],
            [240,'United States Minor Outlying Islands','UMI',1],
            [241,'Uruguay','URY',598],
            [242,'Uzbekistan','UZB',998],
            [243,'Vanuatu','VUT',678],
            [244,'Venezuela','VEN',58],
            [245,'Viet Nam','VNM',84],
            [246,'Virgin Islands, British','VGB',1284],
            [247,'Virgin Islands, U.s.','VIR',1340],
            [248,'Wallis and Futuna','WLF',681],
            [249,'Western Sahara','ESH',212],
            [250,'Yemen','YEM',967],
            [251,'Zambia','ZMB',260],
            [252,'Zimbabwe','ZWE',263]
        ];

        foreach ($countries as $country){

            $country_exists = Country::where('name', $country[1])->first();

            if($country_exists){
                continue;
            }

            $has_childs = in_array($country[1], ['United Kingdom', 'United States', 'Australia', 'Canada']);

            Country::create([
                'name' => $country[1],
                'alpha_3_code' => $country[2],
                'dial' => $country[3],
                'has_childs' => $has_childs
            ]);
        }

        Region::truncate();

        $usa_regions = [
            ['name' => 'Alabama'],
            ['name' => 'Alaska'],
            ['name' => 'American Samoa'],
            ['name' => 'Arizona'],
            ['name' => 'Arkansas'],
            ['name' => 'California'],
            ['name' => 'Colorado'],
            ['name' => 'Connecticut'],
            ['name' => 'Delaware'],
            ['name' => 'District of Columbia'],
            ['name' => 'Florida'],
            ['name' => 'Georgia'],
            ['name' => 'Guam'],
            ['name' => 'Hawaii'],
            ['name' => 'Idaho'],
            ['name' => 'Illinois'],
            ['name' => 'Indiana'],
            ['name' => 'Iowa'],
            ['name' => 'Kansas'],
            ['name' => 'Kentucky'],
            ['name' => 'Louisiana'],
            ['name' => 'Maine'],
            ['name' => 'Maryland'],
            ['name' => 'Massachusetts'],
            ['name' => 'Michigan'],
            ['name' => 'Minnesota'],
            ['name' => 'Mississippi'],
            ['name' => 'Missouri'],
            ['name' => 'Montana'],
            ['name' => 'Nebraska'],
            ['name' => 'Nevada'],
            ['name' => 'New Hampshire'],
            ['name' => 'New Jersey'],
            ['name' => 'New Mexico'],
            ['name' => 'New York'],
            ['name' => 'North Carolina'],
            ['name' => 'North Dakota'],
            ['name' => 'Northern Mariana Islands'],
            ['name' => 'Ohio'],
            ['name' => 'Oklahoma'],
            ['name' => 'Oregon'],
            ['name' => 'Pennsylvania'],
            ['name' => 'Puerto Rico'],
            ['name' => 'Rhode Island'],
            ['name' => 'South Carolina'],
            ['name' => 'South Dakota'],
            ['name' => 'Tennessee'],
            ['name' => 'Texas'],
            ['name' => 'U.S. Minor Outlying Islands'],
            ['name' => 'Utah'],
            ['name' => 'Vermont'],
            ['name' => 'Virginia'],
            ['name' => 'Washington'],
            ['name' => 'West Virginia'],
            ['name' => 'Wisconsin'],
            ['name' => 'Wyoming']
        ];

        $australian_regions = [
            ['name' => 'New South Wales'],
            ['name' => 'Northern Territory'],
            ['name' => 'Queensland'],
            ['name' => 'South Australia'],
            ['name' => 'Tasmania'],
            ['name' => 'Victoria'],
            ['name' => 'Western Australia']
        ];

        $canada_regions = [
            ['name' => 'British Columbia'],
            ['name' => 'Alberta'],
            ['name' => 'Saskatchewan'],
            ['name' => 'Manitoba'],
            ['name' => 'Ontario'],
            ['name' => 'Quebec'],
            ['name' => 'New Brunswick'],
            ['name' => 'Prince Edward Island'],
            ['name' => 'Nova Scotia'],
            ['name' => 'Newfoundland and Labrador'],
            ['name' => 'Yukon'],
            ['name' => 'Northwest Territories'],
            ['name' => 'Nunavut']
        ];

        $uk_regions = [
            ['name' => 'North East'],
            ['name' => 'North West'],
            ['name' => 'Yorkshire and the Humber'],
            ['name' => 'East Midlands'],
            ['name' => 'West Midlands'],
            ['name' => 'East of England'],
            ['name' => 'London'],
            ['name' => 'South East'],
            ['name' => 'South West'],
            ['name' => 'Wales'],
            ['name' => 'Scotland'],
            ['name' => 'Northern Ireland']
        ];

        Region::truncate();

        $usa_id = Country::where('name', 'United States')->first()->id;
        $autralia_id = Country::where('name', 'Australia')->first()->id;
        $canada_id = Country::where('name', 'Canada')->first()->id;
        $uk_id = Country::where('name', 'United Kingdom')->first()->id;

        foreach ($usa_regions as $region){

            $region_exists = Region::where('name', $region['name'])->first();

            if($region_exists){
                continue;
            }

            Region::create(['name' => $region['name'], 'country_id' => $usa_id]);
        }

        foreach ($australian_regions as $region){

            $region_exists = Region::where('name', $region['name'])->first();

            if($region_exists){
                continue;
            }

            Region::create(['name' => $region['name'], 'country_id' => $autralia_id]);
        }

        foreach ($canada_regions as $region){

            $region_exists = Region::where('name', $region['name'])->first();

            if($region_exists){
                continue;
            }

            Region::create(['name' => $region['name'], 'country_id' => $canada_id]);
        }

        foreach ($uk_regions as $region){

            $region_exists = Region::where('name', $region['name'])->first();

            if($region_exists){
                continue;
            }

            Region::create(['name' => $region['name'], 'country_id' => $uk_id]);
        }

        MicromorgiPackage::truncate();

        $packages = [
            ['micromorgi_count' => '200', 'price' => '10', 'sort_order' => '1'],
            ['micromorgi_count' => '500', 'price' => '25', 'sort_order' => '2'],
            ['micromorgi_count' => '1000', 'price' => '50', 'sort_order' => '3'],
            ['micromorgi_count' => '2000', 'price' => '100', 'sort_order' => '4']
        ];

        foreach ($packages as $package){

            if(!MicromorgiPackage::where('micromorgi_count', $package['micromorgi_count'])->first()){
                MicromorgiPackage::create($package);
            }
        }

        $groups = [
            'a', 'b', 'c'
        ];

        UserABGroup::truncate();

        foreach ($groups as $group){

            if(!UserABGroup::where('name', $group)->first()){
                UserABGroup::create(['name' => $group]);
            }
        }

        Gender::truncate();

        $genders = [
            [
                'name' => 'Male',
                'key_name' => 'male'
            ],
            [
                'name' => 'Female',
                'key_name' => 'female'
            ],
            [
                'name' => 'Other',
                'key_name' => 'other'
            ]
        ];

        foreach ($genders as $gender){

            if(!Gender::where('key_name', $gender['key_name'])->first()){
                Gender::create($gender);
            }

        }

        PaymentPlatform::truncate();

        $platforms = [
            [
                'name' => 'PayPal',
                'description' => 'https://www.paypal.com/',
                'fields' => '{"email": "Email"}'
            ],
            [
                'name' => 'ePay',
                'description' => 'https://www.epay.com/',
                'fields' => '{"email": "Email"}'
            ],
            [
                'name' => 'Paxum',
                'description' => 'https://eu.paxum.com/',
                'fields' => '{"email": "Email"}'
            ],
        ];

        foreach ($platforms as $platform){

            if(!PaymentPlatform::where('name', $platform['name'])->first()){
                PaymentPlatform::create($platform);
            }
        }

        TransactionType::truncate();

        $types = [
            ['type' => 'gift', 'lang' => 'EN', 'description' => 'Recurring Morgi montly gift from <user_from>'],
            ['type' => 'chat', 'lang' => 'EN', 'description' => 'Gift Micro Morgi on chat from <user_from>'],
            ['type' => 'bought_micromorgi', 'lang' => 'EN', 'description' => 'Purchase of n.<amount_micromorgi> micromorgi (<amount_dollars>)'],
            ['type' => 'refund', 'lang' => 'EN', 'description' => 'System refund following an error to <user_to> (#<referal_external_id>)'],
            ['type' => 'withdrawal', 'lang' => 'EN', 'description' => 'Withdrawal <payment_method> (<payment_info>)'],
            ['type' => 'withdrawal_rejected', 'lang' => 'EN', 'description' => 'Withdrawal <payment_method> rejected (original payment: #<referal_external_id>)'],
            ['type' => 'bonus', 'lang' => 'EN', 'description' => 'Bonus from Morgi'],
        ];

        foreach ($types as $type){

            $transaction_type = TransactionType::where('type', $type['type'])->where('lang', $type['lang'])->first();

            if(!$transaction_type){
                TransactionType::create($type);
                continue;
            }

            $transaction_type->update($type);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
