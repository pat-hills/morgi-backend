<?php

use App\Models\Path;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \App\Models\Path::truncate();
        $paths = [
            "An Activist" => ["Climate Change Activist",  "Political Activist", "Social Activist"],
            "An Actor" => ["Film Actor", "Stunt Person", "Theatre Actor",  "TV Actor", "Voice Actor"],
            "An Archaeologist" => null,
            "An Architect" => null,
            "An A Bartender" => null,
            "A Beautician" => ["Cosmetologist", "Makeup Artist", "Nail Artist"],
            "A Bodybuilder " => null,
            "A Bodyguard " => null,
            "A Business Owner" => ["Beauty Salon", "Caterer", "Coffee Shop", "Gallerist", "Restaurant", "Retail", "Supermarket"],
            "A Captain " => null,
            "A Caregiver" => ["Au Pair", "Butler", "Dog Walker", "Personal Assistant"],
            "A Chef" => ["Chinese Cuisine", "French Cuisine", "Indian Cuisine", "Italian Cuisine", "Japanese Cuisine", "Mediterranean Cuisine", "Pastry Chef", "Vegan Chef"],
            "A Classic Musician" => ["Composer", "Director", "Opera Singer", "Orchestral Musician", "Pianist", "String Player", "Wind Player"],
            "A Coacher" => ["Business Coach", "Existential Therapist", "Humanistic", "Lifestyle Coach", "Mental Coach", "Relational Therapist"],
            "A Companion" => null,
            "A Concierge" => null,
            "A Dancer" => ["Ballet Dancer", "Choreographer", "Hip Hop Dancer", "Modern Dancer"],
            "A Driver" => ["Chauffeur", "Racing Driver", "Touring Car Racer", "Train Driver", "Truck Driver"],
            "An Economist" => null,
            "An Engineer" => null,
            "An Entrepreneur" => null,
            "A Farmer" => ["Chicken", "Cows", "Goat", "Horse", "Sheep", "Vegetables", "Winemaker"],
            "A Fashion Professional" => ["Fashionista", "Personal Shopper", "Personal Stylist"],
            "A Flight Attendant" => null,
            "A Good Person" => null,
            "A Graphic Designer" => null,
            "A Hairdresser" => ["Barber", "Celebrity Hairstylist", "Hairstylist"],
            "An Influencer" => ["Beauty", "Blogger", "Fashion", "Fitness", "Food", "Gaming", "Photography", "Social Media", "Travel", "YouTube"],
            "A Lawyer" => null,
            "A Leader" => null,
            "A Magician" => null,
            "A Manager" => null,
            "A Medical Doctor" => ["General", "Plastic Surgeon", "Psychologist", "Surgeon"],
            "A Model" => ["Face", "Fashion", "Fitness", "Hair", "Plus-Size", "Swimsuit"],
            "A Musician" => ["Blues", "Country", "DJ", "Jazz", "Pop Star", "R&B / Soul", "Rapper", "Rock", "Songwriter"],
            "A Mystic" => ["Astrologist", "Kabbalist", "Numerologist", "Occultist", "Tarot Reader"],
            "A Nurse" => null,
            "A Nutritionist" => ["Dietitian", "Sport", "Holistic"],
            "A Painter" => ["Abstract", "Commercial", "Cubist", "Expressionist", "Impressionist", "Modernist", "Surrealist", "Tattooist"],
            "A Good Parent" => null,
            "A Politician" => null,
            "A Programmer" => null,
            "A Pilot" => ["Aerobatic Pilot", "Fighter Pilot", "Spaceship Pilot"],
            "A Preacher" => ["Iman", "Nun", "Monk", "Priest", "Pujari", "Rabbi", "Shinshoku", "Taoist"],
            "Rich" => null,
            "A Scientist" => ["Astronomer", "Biologist", "Chemist", "Computer Science", "Mathematician", "Physicist"],
            "A Sculpturer" => null,
            "A Singer" => ["Alternative Singer", "Classical Singer", "Hip Hop Singer", "Pop Singer", "Rock Singer",  "Soul/Rap Singer"],
            "Something Else" => null,
            "A Social Worker" => null,
            "A Sportsman" => ["American Football", "Athlete", "Baseball Player", "Basketball Player", "Boxer",
                "Cricketer", "Cyclist", "Golfer", "Gymnast", "Kickboxer", "Martial Artist", "Runner", "Soccer Player", "Swimmer", "Volleyball Player"],
            "A Star" => null,
            "A Teacher" => ["Art", "Etiquette", "Language", "School", "Special Educator", "University Professor"],
            "A Tutor" => null,
            "A Tour Guide" => null,
            "A Trader" => ["Commodity", "Crypto", "Day", "Forex", "Stock"],
            "A Veterinarian" => null,
            "A Writer" => ["Editor", "Journalist", "Movie Screenwriter",  "Novelist"],
            "A Yoga Instructor" => null,
            "A Zoologist" => null
        ];

        foreach ($paths as $name=>$path){

            $p = Path::create(['name' => $name, 'key_name' => strtolower(str_replace(' ', '_', $name))]);

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
        //
    }
}
