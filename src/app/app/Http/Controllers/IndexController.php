<?php

namespace App\Http\Controllers;

use App\Mail\ContactForm;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactFormRequest;

class IndexController extends Controller
{
    public function index()
    {
        $products = Product::orderBy("status", "ASC")->get();

        return view('welcome', [
            "products" => $products,
        ]);
    }

    //метод отвечает за вывод формы
    public function getContactForm()
    {
        return view('forms.contact_form');
    }

    //метод отвечает за обработку формы
    public function contactForm(ContactFormRequest $request)
    {
        Mail::to(env('PGADMIN_EMAIL'))->send(new ContactForm($request->validated()));

        return redirect(route("contact_form"));
    }
}
