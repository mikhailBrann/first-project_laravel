<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\AdminUser;

class CreateProductNotific extends Mailable
{
    use Queueable, SerializesModels;

    protected $product;
    protected $user;

    /**
     * Create a new message instance.
     * @param  App\Models\Product  $product
     * @param App\Models\AdminUser $user
     * @return void
     */
    public function __construct(Product $product, AdminUser $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.admin.create_product_notif')->with([
            'productName' => $this->product->name,
            'productArticle' => $this->product->article,
            'productCreateDate' => $this->product->created_at,
            'userName' => $this->user->name,
            'userEmail' => $this->user->email,
        ]);
    }
}
