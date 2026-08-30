<?php

namespace App\Http\Controllers\App\Profile;

use App\Actions\GeneratePDF;
use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Contracts\View\View;

class BillingController extends Controller
{
    public function index(): View
    {
        /** @var User **/
        $user = auth()->user();
        $courses = $user->courses()
            ->where('is_free', false)
            ->with('media')
            ->get();

        return view('app.profile.billing', [
            'courses' => $courses,
        ]);
    }

    public function courseInvoice(Course $course)
    {
        /** @var Invoice **/
        $invoice = Invoice::where('user_id', auth()->id())
            ->where("invoiceable_id", $course->id)
            ->where("invoiceable_type", "App\Models\Courses\Course")
            ->first();

        $filename = 'inv-' . $invoice->id . '.pdf';
        // if ($invoice->hasMedia('invoices')) {
        //     return response()->file($invoice->getMedia('invoices')[0]->getPath(), [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' =>  'inline; filename="' . $filename . '"',
        //     ]);
        // }

        $pdf = (new GeneratePDF)->execute('app.pdf.invoice', [
            'invoice' => $invoice,
            'data' => $course,
            'user' => auth()->user()
        ]);

        $stream = $pdf->stream($filename);
        $invoice->addMediaFromStream($stream)->usingFileName($filename)->toMediaCollection('invoices', 'private');
        return $stream;
    }
}
