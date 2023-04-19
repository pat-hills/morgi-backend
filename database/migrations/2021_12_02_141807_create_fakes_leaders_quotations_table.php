<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFakesLeadersQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fakes_leaders_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->dateTime('leader_since');
            $table->integer('dollar_amount_gifted');
            $table->text('text');
            $table->timestamps();
        });

        $fakes = [
            ['username' => 'Aria', 'leader_since' => '2021-12-2 10:18:44', 'dollar_amount_gifted' => 8000, 'text' => "Giving makes me happy because I feel like I’m creating an impact, investing in someone’s future – knowing that my gift is helping boost their confidence and inspiring their road."],
            ['username' => 'Valerie', 'leader_since' => '2021-12-2 6:26:22', 'dollar_amount_gifted' => 6000, 'text' => "Giving makes me happy because I get the chance to help and share my stories with the future generation, and this is the most fulfilling feeling I had ever had. I love using Morgi because I get to track and receive updates on my Rookies’ progress."],
            ['username' => 'Mia', 'leader_since' => '2021-12-2 11:54:36', 'dollar_amount_gifted' => 6000, 'text' => "Giving makes me happy because I get to be the Leader that I never had and the mentor that I always wanted. I can give them advice about my industry and help them make their mark on this world." ],
            ['username' => 'Stacy', 'leader_since' => '2021-12-2 1:28:10', 'dollar_amount_gifted' => 5000, 'text' => "Giving makes me happy because I just love giving. Nothing makes me happier in this world but the sense of gifting someone who needs it. And who knows what lies in the future and who’s going to remember you, right?" ],
            ['username' => 'Bella', 'leader_since' => '2021-12-2 18:40:12', 'dollar_amount_gifted' => 5000, 'text' => "Giving makes me happy because I still remember my favorite teacher, and now I get the opportunity to be that amazing teacher for others. By guiding others with the skills that this teacher taught me, I feel more fulfilled than ever before." ],
            ['username' => 'Amanda', 'leader_since' => '2021-12-2 12:32:24', 'dollar_amount_gifted' => 2000, 'text' => "Giving makes me happy because I always thought that using my money to buy new things would make me happy and fulfilled, but that was before I got the chance to make a difference through Morgi. Gifting means the world for me now, and this is something I will always cherish." ],
            ['username' => 'Olivia', 'leader_since' => '2021-12-2 17:01:53', 'dollar_amount_gifted' => 1000, 'text' => "Giving makes me happy because I get to use my life experience to help others and guide them to make the best choices in life." ],
        ];

        foreach ($fakes as $fake){
            \App\Models\FakeLeaderQuotation::create($fake);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fakes_leaders_quotations');
    }
}
