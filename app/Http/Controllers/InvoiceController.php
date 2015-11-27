<?php

namespace Calctool\Http\Controllers;

use Illuminate\Http\Request;

use \Calctool\Models\Project;
use \Calctool\Models\Invoice;
use \Calctool\Models\InvoiceVersion;
use \Calctool\Models\Offer;
use \Calctool\Models\Resource;
use \Calctool\Calculus\InvoiceTerm;

use \Auth;
use \PDF;

class InvoiceController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function doUpdateCode(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer')
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice->reference = $request->get('reference');
		$invoice->book_code = $request->get('bookcode');

		$invoice->save();

		return json_encode(['success' => 1]);
	}

	public function doUpdateDescription(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer')
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice->description = $request->get('description');
		$invoice->closure = $request->get('closure');

		$invoice->save();

		return json_encode(['success' => 1]);
	}

	public function doUpdateCondition(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer'),
			'condition' => array('required','integer')
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice->payment_condition = $request->get('condition');

		$invoice->save();

		return json_encode(['success' => 1]);
	}

	public function doInvoiceVersionNew(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer'),
			'projectid' => array('required','integer'),
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$project = Project::find($request->get('projectid'));
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice_version = new InvoiceVersion;

		if ($request->get('include-tax'))
			$invoice_version->include_tax = true;
		else
			$invoice_version->include_tax = false;
		if ($request->get('only-totals'))
			$invoice_version->only_totals = true;
		else
			$invoice_version->only_totals = false;
		if ($request->get('seperate-subcon'))
			$invoice_version->seperate_subcon = true;
		else
			$invoice_version->seperate_subcon = false;
		if ($request->get('display-worktotals'))
			$invoice_version->display_worktotals = true;
		else
			$invoice_version->display_worktotals = false;
		if ($request->get('display-specification'))
			$invoice_version->display_specification = true;
		else
			$invoice_version->display_specification = false;
		if ($request->get('display-description'))
			$invoice_version->display_description = true;
		else
			$invoice_version->display_description = false;

		$invoice_version->amount = $invoice->amount;
		$invoice_version->rest_21 = $invoice->rest_21;
		$invoice_version->rest_6 = $invoice->rest_6;
		$invoice_version->rest_0 = $invoice->rest_0;
		$invoice_version->description = $invoice->description;
		$invoice_version->closure = $invoice->closure;
		$invoice_version->reference = $invoice->reference;
		$invoice_version->book_code = $invoice->book_code;
		$invoice_version->invoice_code = $invoice->invoice_code;
		$invoice_version->payment_condition = $invoice->payment_condition;
		$invoice_version->to_contact_id = $request->get('to_contact');
		$invoice_version->from_contact_id = $request->get('from_contact');
		$invoice_version->invoice_id = $invoice->id;

		$invoice_version->save();

		$page = 0;
		$newname = Auth::id().'-'.substr(md5(uniqid()), 0, 5).'-'.$invoice_version->invoice_code.'-invoice.pdf';
		if ($invoice->isclose) {
			$pdf = PDF::loadView('calc.invoice_pdf', ['invoice' => $invoice_version]);
		} else {
			$pdf = PDF::loadView('calc.invoice_term_pdf', ['invoice' => $invoice_version]);
		}
		$pdf->setOption('footer-html','http://localhost/c4586v34674v4&vwasrt/footer_pdf?uid='.Auth::id()."&page=".$page++);
		$pdf->save('user-content/'.$newname);

		$resource = new Resource;
		$resource->resource_name = $newname;
		$resource->file_location = 'user-content/' . $newname;
		$resource->file_size = filesize('user-content/' . $newname);
		$resource->user_id = Auth::id();
		$resource->description = 'Factuurversie';

		$resource->save();

		$invoice_version->resource_id = $resource->id;

		$invoice_version->save();

		return redirect('invoice/project-'.$project->id.'/invoice-version-'.$invoice_version->id);
	}

	public function doInvoiceNewTerm(Request $request)
	{
		$this->validate($request, [
			'projectid' => array('required','integer')
		]);

		$project = Project::find($request->get('projectid'));
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$offer_last = Offer::where('project_id','=',$project->id)->orderBy('created_at', 'desc')->first();
		$cnt = Invoice::where('offer_id','=', $offer_last->id)->count();
		if ($cnt>1) {
			$invoice = Invoice::where('offer_id','=', $offer_last->id)->where('isclose','=',false)->orderBy('priority', 'desc')->first();

			$ninvoice = new Invoice;
			$ninvoice->payment_condition = $invoice->payment_condition;
			$ninvoice->invoice_code = $invoice->invoice_code;
			$ninvoice->priority = $invoice->priority+1;
			$ninvoice->offer_id = $invoice->offer_id;
			$ninvoice->save();
		} else {
			$ninvoice = new Invoice;
			$ninvoice->payment_condition = 1;
			$ninvoice->invoice_code = 'Concept';
			$ninvoice->priority = 1;
			$ninvoice->offer_id = $offer_last->id;
			$ninvoice->save();
		}

		return back();
	}

	public function doInvoiceDeleteTerm(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer')
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice->delete();

		return back()->with('success', 'Termijnfactuur verwijderd');
	}

	public function doUpdateAmount(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer'),
			'project' => array('required','integer')
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}
		$project = Project::find($request->get('project'));
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$amount = str_replace(',', '.', str_replace('.', '' , $request->get('amount')));
		$total = str_replace(',', '.', str_replace('.', '' , $request->get('totaal')));

		$invoice->amount = $amount;
		$invoice->rest_21 = InvoiceTerm::partTax1($project, $invoice)*$amount;
		$invoice->rest_6 = InvoiceTerm::partTax2($project, $invoice)*$amount;
		$invoice->rest_0 = InvoiceTerm::partTax3($project, $invoice)*$amount;
		$invoice->save();

		$cnt = Invoice::where('offer_id','=', $invoice->offer_id)->count();
		if ($cnt>1) {
			$invoice = Invoice::where('offer_id','=', $invoice->offer_id)->where('isclose','=',true)->first();
			$invoice->amount = $total;
			$invoice->rest_21 = InvoiceTerm::partTax1($project, $invoice)*$total;
			$invoice->rest_6 = InvoiceTerm::partTax2($project, $invoice)*$total;
			$invoice->rest_0 = InvoiceTerm::partTax3($project, $invoice)*$total;
			$invoice->save();
		}

		return json_encode(['success' => 1]);
	}

	public function doInvoiceClose(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer'),
			'projectid' => array('required','integer'),
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$project = Project::find($request->get('projectid'));
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice_version = InvoiceVersion::where('invoice_id',$invoice->id)->orderBy('created_at','desc')->first();
		$invoice->include_tax = $invoice_version->include_tax;
		$invoice->only_totals = $invoice_version->only_totals;
		$invoice->seperate_subcon = $invoice_version->seperate_subcon;
		$invoice->display_worktotals = $invoice_version->display_worktotals;
		$invoice->display_specification = $invoice_version->display_specification;
		$invoice->display_description = $invoice_version->display_description;
		$invoice->description = $invoice_version->description;
		$invoice->closure = $invoice_version->closure;
		$invoice->to_contact_id = $invoice_version->to_contact_id;
		$invoice->from_contact_id = $invoice_version->from_contact_id;
		$invoice->invoice_close = true;
		$invoice->invoice_code = InvoiceController::getInvoiceCode($project->id);
		$invoice->bill_date = date('Y-m-d H:i:s');

		$invoice->save();

		$page = 0;
		$newname = Auth::id().'-'.substr(md5(uniqid()), 0, 5).'-'.$invoice->invoice_code.'-invoice.pdf';
		if ($invoice->isclose) {
			$pdf = PDF::loadView('calc.invoice_final_pdf', ['invoice' => $invoice]);
		} else {
			$pdf = PDF::loadView('calc.invoice_term_final_pdf', ['invoice' => $invoice]);
		}
		$pdf->setOption('footer-html','http://localhost/c4586v34674v4&vwasrt/footer_pdf?uid='.Auth::id()."&page=".$page++);
		$pdf->save('user-content/'.$newname);

		$resource = new Resource;
		$resource->resource_name = $newname;
		$resource->file_location = 'user-content/' . $newname;
		$resource->file_size = filesize('user-content/' . $newname);
		$resource->user_id = Auth::id();
		$resource->description = 'Factuurversie';

		$resource->save();

		$invoice->resource_id = $resource->id;

		$invoice->save();

		Auth::user()->invoice_counter++;
		Auth::user()->save();

		return redirect('/invoice/project-'.$project->id);
	}

	public function doInvoicePay(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer'),
			'projectid' => array('required','integer'),
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice->payment_date = date('Y-m-d');

		$invoice->save();

		return json_encode(['success' => 1, 'payment' => date('d-m-Y')]);
	}

	public function doInvoiceCloseAjax(Request $request)
	{
		$this->validate($request, [
			'id' => array('required','integer'),
			'projectid' => array('required','integer'),
		]);

		$invoice = Invoice::find($request->get('id'));
		if (!$invoice)
			return json_encode(['success' => 0]);
		$offer = Offer::find($invoice->offer_id);
		if (!$offer)
			return json_encode(['success' => 0]);
		$project = Project::find($offer->project_id);
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$project = Project::find($request->get('projectid'));
		if (!$project || !$project->isOwner()) {
			return json_encode(['success' => 0]);
		}

		$invoice->invoice_close = true;
		$invoice->invoice_code = InvoiceController::getInvoiceCode($project->id);
		$invoice->bill_date = date('Y-m-d H:i:s');

		$invoice->save();
		Auth::user()->invoice_counter++;
		Auth::user()->save();

		return json_encode(['success' => 1, 'billing' => date('d-m-Y')]);
	}

	/* id = $project->id */
	public static function getInvoiceCode($id)
	{
		return sprintf("%s%05d-%03d-%s", Auth::user()->invoicenumber_prefix, $id, Auth::user()->invoice_counter, date('y'));
	}

	/* id = $project->id */
	public static function getInvoiceCodeConcept($id)
	{
		return sprintf("%s%05d-CONCEPT-%s", Auth::user()->invoicenumber_prefix, $id, date('y'));
	}

}
