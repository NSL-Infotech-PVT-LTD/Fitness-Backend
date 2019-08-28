<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller {

    public static $__stripeIdPrefix = "subscrip_";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $subscription = Subscription::where('stripe_id', 'LIKE', "%$keyword%")
                            ->orWhere('name', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $subscription = Subscription::latest()->paginate($perPage);
        }

        return view('admin.subscriptions.index', compact('subscription'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {

        $requestData = $request->all();

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $model = Subscription::create($requestData);
        $stripe = \Stripe\Plan::create([
                    "amount" => $model->price * 100,
                    "interval" => "month",
                    "product" => [
                        "name" => $model->name
                    ],
                    "currency" => "gbp",
                    "id" => self::$__stripeIdPrefix . $model->id
        ]);
//        dd($stripe->product);
        $model->stripe_id = $stripe->product;
        $model->save();
        return redirect('admin/subscriptions')->with('flash_message', 'Subscription added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $subscription = Subscription::findOrFail($id);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $subscription = Subscription::findOrFail($id);

        return view('admin.subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id) {

        $requestData = $request->all();

        $subscription = Subscription::findOrFail($id);
        $subscription->update($requestData);
        \Stripe\Stripe::setApiKey("sk_test_6gc1F4O9Q7o034KfDja2Jlcz00NaQzLyMp");

        \Stripe\Plan::update(
                self::$__stripeIdPrefix . $id,
                [
                    "amount" => $subscription->price * 100
                ]
        );
        return redirect('admin/subscriptions')->with('flash_message', 'Subscription updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        $model = Subscription::whereId($id)->first();
        if (Subscription::destroy($id)) {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $plan = \Stripe\Plan::retrieve(self::$__stripeIdPrefix . $id);
                $plan->delete();
                $product = \Stripe\Product::retrieve($model->stripe_id);
                $product->delete();
            } catch (\Exception $ex) {
                $data = $ex->getMessage();
            }
        }
        return redirect('admin/subscriptions')->with('flash_message', 'Subscription deleted!');
    }

}
