<?php

use App\Models\Path;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReSeedPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->string('prepend')->nullable(true);
        });

        \App\Models\Path::truncate();
        $paths = [
            "Activist" => ["Climate Change Activist",  "Political Activist", "Social Activist"],
            "Actor" => ["Film Actor", "Stunt Person", "Theatre Actor",  "TV Actor", "Voice Actor"],
            "Archaeologist" => null,
            "Architect" => null,
            "Bartender" => null,
            "Beautician" => ["Cosmetologist", "Makeup Artist", "Nail Artist"],
            "Bodybuilder " => null,
            "Bodyguard " => null,
            "Business Owner" => ["Beauty Salon", "Caterer", "Coffee Shop", "Gallerist", "Restaurant", "Retail", "Supermarket"],
            "Captain " => null,
            "Caregiver" => ["Au Pair", "Butler", "Dog Walker", "Personal Assistant"],
            "Chef" => ["Chinese Cuisine", "French Cuisine", "Indian Cuisine", "Italian Cuisine", "Japanese Cuisine", "Mediterranean Cuisine", "Pastry Chef", "Vegan Chef"],
            "Classic Musician" => ["Composer", "Director", "Opera Singer", "Orchestral Musician", "Pianist", "String Player", "Wind Player"],
            "Coacher" => ["Business Coach", "Existential Therapist", "Humanistic", "Lifestyle Coach", "Mental Coach", "Relational Therapist"],
            "Companion" => null,
            "Concierge" => null,
            "Dancer" => ["Ballet Dancer", "Choreographer", "Hip Hop Dancer", "Modern Dancer"],
            "Driver" => ["Chauffeur", "Racing Driver", "Touring Car Racer", "Train Driver", "Truck Driver"],
            "Economist" => null,
            "Engineer" => null,
            "Entrepreneur" => null,
            "Farmer" => ["Chicken", "Cows", "Goat", "Horse", "Sheep", "Vegetables", "Winemaker"],
            "Fashion Professional" => ["Fashionista", "Personal Shopper", "Personal Stylist"],
            "Flight Attendant" => null,
            "Good Person" => null,
            "Graphic Designer" => null,
            "Hairdresser" => ["Barber", "Celebrity Hairstylist", "Hairstylist"],
            "Influencer" => ["Beauty", "Blogger", "Fashion", "Fitness", "Food", "Gaming", "Photography", "Social Media", "Travel", "YouTube"],
            "Lawyer" => null,
            "Leader" => null,
            "Magician" => null,
            "Manager" => null,
            "Medical Doctor" => ["General", "Plastic Surgeon", "Psychologist", "Surgeon"],
            "Model" => ["Face", "Fashion", "Fitness", "Hair", "Plus-Size", "Swimsuit"],
            "Musician" => ["Blues", "Country", "DJ", "Jazz", "Pop Star", "R&B / Soul", "Rapper", "Rock", "Songwriter"],
            "Mystic" => ["Astrologist", "Kabbalist", "Numerologist", "Occultist", "Tarot Reader"],
            "Nurse" => null,
            "Nutritionist" => ["Dietitian", "Sport", "Holistic"],
            "Painter" => ["Abstract", "Commercial", "Cubist", "Expressionist", "Impressionist", "Modernist", "Surrealist", "Tattooist"],
            "Good Parent" => null,
            "Politician" => null,
            "Programmer" => null,
            "Pilot" => ["Aerobatic Pilot", "Fighter Pilot", "Spaceship Pilot"],
            "Preacher" => ["Iman", "Nun", "Monk", "Priest", "Pujari", "Rabbi", "Shinshoku", "Taoist"],
            "Rich" => null,
            "Scientist" => ["Astronomer", "Biologist", "Chemist", "Computer Science", "Mathematician", "Physicist"],
            "Sculpturer" => null,
            "Singer" => ["Alternative Singer", "Classical Singer", "Hip Hop Singer", "Pop Singer", "Rock Singer",  "Soul/Rap Singer"],
            "Something Else" => null,
            "Social Worker" => null,
            "Sportsman" => ["American Football", "Athlete", "Baseball Player", "Basketball Player", "Boxer",
                "Cricketer", "Cyclist", "Golfer", "Gymnast", "Kickboxer", "Martial Artist", "Runner", "Soccer Player", "Swimmer", "Volleyball Player"],
            "Star" => null,
            "Teacher" => ["Art", "Etiquette", "Language", "School", "Special Educator", "University Professor"],
            "Tutor" => null,
            "Tour Guide" => null,
            "Trader" => ["Commodity", "Crypto", "Day", "Forex", "Stock"],
            "Veterinarian" => null,
            "Writer" => ["Editor", "Journalist", "Movie Screenwriter",  "Novelist"],
            "Yoga Instructor" => null,
            "Zoologist" => null
        ];

        $prepends = [
            "Activist" => "An",
            "Actor" => "An",
            "Archaeologist" => "An",
            "Architect" => "An",
            "Bartender" => "An",
            "Beautician" => "A",
            "Bodybuilder " => "A",
            "Bodyguard " => "A",
            "Business Owner" => "A",
            "Captain " => "A",
            "Caregiver" => "A",
            "Chef" => "A",
            "Classic Musician" => "A",
            "Coacher" => "A",
            "Companion" => "A",
            "Concierge" => "A",
            "Dancer" => "A",
            "Driver" => "A",
            "Economist" => "An",
            "Engineer" => "An",
            "Entrepreneur" => "An",
            "Farmer" => "A",
            "Fashion Professional" => "A",
            "Flight Attendant" => "A",
            "Good Person" => "A",
            "Graphic Designer" => "A",
            "Hairdresser" => "A",
            "Influencer" => "An",
            "Lawyer" => "A",
            "Leader" => "A",
            "Magician" => "A",
            "Manager" => "A",
            "Medical Doctor" => "A",
            "Model" => "A",
            "Musician" => "A",
            "Mystic" => "A",
            "Nurse" => "A",
            "Nutritionist" => "A",
            "Painter" => "A",
            "Good Parent" => "A",
            "Politician" => "A",
            "Programmer" => "A",
            "Pilot" => "A",
            "Preacher" => "A",
            "Scientist" => "A",
            "Sculpturer" => "A",
            "Singer" => "A",
            "Social Worker" => "A",
            "Sportsman" => "A",
            "Star" => "A",
            "Teacher" => "A",
            "Tutor" => "A",
            "Tour Guide" => "A",
            "Trader" => "A",
            "Veterinarian" => "A",
            "Writer" => "A",
            "Yoga Instructor" => "A",
            "Zoologist" => "A"
        ];

        foreach ($paths as $name=>$path){

            $prepend = (isset($prepends[$name])) ? $prepends[$name] : null;

            $p = Path::create(['name' => $name, 'key_name' => strtolower(str_replace(' ', '_', $name)), 'prepend' => $prepend]);

            if(isset($path)){
                foreach ($path as $sp){
                    Path::create(['name' => $sp, 'key_name' => strtolower(str_replace(' ', '_', $sp)),
                        "is_subpath" => true, "parent_id" => $p->id]);
                }
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->dropColumn('prepend');
        });
    }
}
