<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;

class CheckOverdueBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark borrowed books as overdue if the return date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdueCount = Transaction::where('status', 'borrowed')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $this->info("Successfully marked {$overdueCount} transactions as overdue.");
    }
}
