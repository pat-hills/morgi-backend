<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFakeQuotationsText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $leader_quotations_plural = \App\Models\FakeLeaderQuotation::query()
            ->where('text', 'LIKE', "%Leaders%")
            ->get();

        foreach ($leader_quotations_plural as $leader_quotation){
            $text = str_replace('Leaders', 'Morgi Friends', $leader_quotation->text);
            $leader_quotation->update([
                'text' => $text
            ]);
        }

        $leader_quotations = \App\Models\FakeLeaderQuotation::query()
            ->where('text', 'LIKE', "%Leader%")
            ->get();

        foreach ($leader_quotations as $leader_quotation){
            $text = str_replace('Leader', 'Morgi Friend', $leader_quotation->text);
            $leader_quotation->update([
                'text' => $text
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $leader_quotations_plural = \App\Models\FakeLeaderQuotation::query()
            ->where('text', 'LIKE', "%Morgi Friends%")
            ->get();

        foreach ($leader_quotations_plural as $leader_quotation){
            $text = str_replace('Morgi Friends', 'Leaders', $leader_quotation->text);
            $leader_quotation->update([
                'text' => $text
            ]);
        }

        $leader_quotations = \App\Models\FakeLeaderQuotation::query()
            ->where('text', 'LIKE', "%Morgi Friend%")
            ->get();

        foreach ($leader_quotations as $leader_quotation){
            $text = str_replace('Morgi Friend', 'Leader', $leader_quotation->text);
            $leader_quotation->update([
                'text' => $text
            ]);
        }
    }
}
