<?php

namespace App\Jobs;

use App\Mail\CreateProductNotific;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CreateProductNotificJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to(env('PGADMIN_EMAIL'))->send(new CreateProductNotific($this->product, $this->user));
    }
}
