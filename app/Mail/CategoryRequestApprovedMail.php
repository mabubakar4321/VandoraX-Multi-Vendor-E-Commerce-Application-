<?php

namespace App\Mail;

use App\Models\Category;
use App\Models\VendorCategoryRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CategoryRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public VendorCategoryRequest $request,
        public Category $category
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->request->vendor->user->email, $this->request->vendor->user->name)],
            subject: '✅ Your Category Request Has Been Approved - ' . $this->category->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.category-request-approved',
        );
    }
}