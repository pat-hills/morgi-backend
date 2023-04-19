<?php

namespace App\Console\Commands;


use App\Sendgrid\SendgridUtils;
use Illuminate\Console\Command;

class SendGridEmailsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendgrid:reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and add to blacklist emails from SendGrid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('SendGrid emails check started..');

        echo "\n";
        $this->info('SPAM_REPORTS | STARTING');
        $spams = SendgridUtils::handleSpamReports();
        $status_spams = ($spams['status']==='error') ? 'ERROR' : 'DONE';
        $this->info('SPAM_REPORTS | ' . $status_spams);

        echo "\n";
        $this->info('BLOCKS | STARTING');
        $blocks = SendgridUtils::handleBlockReports();
        $status_blocks = ($blocks['status']==='error') ? 'ERROR' : 'DONE';
        $this->info('BLOCKS | ' . $status_blocks);

        echo "\n";
        $this->info('INVALID_EMAILS | STARTING');
        $invalid_emails = SendgridUtils::handleInvalidEmailsReports();
        $status_invalid_emails = ($invalid_emails['status']==='error') ? 'ERROR' : 'DONE';
        $this->info('INVALID_EMAILS | ' . $status_invalid_emails);

        echo "\n";
        $this->info('BOUNCES | STARTING');
        $bounces = SendgridUtils::handleBouncesReports();
        $status_bounces = ($bounces['status']==='error') ? 'ERROR' : 'DONE';
        $this->info('BOUNCES | ' . $status_bounces);

        return 0;
    }
}
