@extends('layout.popup')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <div class="row mt-4">
            <div class="card text-white bg-secondary mb-3 col-lg-12" id="order_basic_info">
                <div class="card-body">
                    <h6 class="card-title" id="order_id">Order ID:</h6>
                    <h6 class="card-title" id="date_ordered">Ordered On:</h6>
                </div>
            </div>

            <div class="card bg-light mb-3 col-lg-12">
                <div class="card-header"><h4>Product Information</h4></div>
                <div class="card-body">
                    <table class="table table-bordered" id="items">
                        <thead class="text-center">
                            <th>Mark/Unmark</th>
                            <th>
                                <strong>Item Code</strong>/
                                <p>Shipment Number</p>
                            </th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Variant</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Product Sub-total</th>
                        </thead>

                        <tbody class="text-center">
                        <tr class="item-template" hidden>
                            <td class="mark_unmark"></td>
                            <td class="item_code"></td>
                            <td class="prod_code"></td>
                            <td class="prod_name"></td>
                            <td class="prod_variant"></td>
                            <td class="qty"></td>
                            <td class="price"></td>
                            <td class="sub_total"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card bg-light mb-3 col-lg-12">
                <div class="card-header"><h4>Customer Information</h4></div>
                <div class="card-body">
                    <table class="table table-bordered" id="customer_info">
                        <tbody>
                        <colgroup>
                            <col style="width: 10%">
                            <col style="width: 30%">
                            <col style="width: 10%">
                            <col style="width: 30%">
                        </colgroup>

                        <tr>
                            <th>Customer ID</th>
                            <td id="buyer"></td>

                            <th>Email Address</th>
                            <td id="buyer_email"></td>
                        </tr>

                        <tr>
                            <th>Phone</th>
                            <td id="buyer_phone"></td>

                            <th>Mobile</th>
                            <td id="buyer_mobile"></td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card bg-light mb-3 col-lg-12">
                <div class="card-header"><h4>Shipping Information</h4></div>
                <div class="card-body">
                    <table class="table table-bordered" id="shipping_info">
                        <tbody border="1">
                        <colgroup>
                            <col style="width: 10%">
                            <col style="width: 30%">
                            <col style="width: 10%">
                            <col style="width: 30%">
                        </colgroup>
                        <tr>
                            <th>Recipient</th>
                            <td id="receiver_name"></td>

                            <th>Recipient (English)</th>
                            <td id="rec_japan_name"></td>
                        </tr>

                        <tr>
                            <th>Phone</th>
                            <td id="receiver_phone"></td>

                            <th>Mobile</th>
                            <td id="receiver_mobile"></td>
                        </tr>

                        <tr>
                            <th>Country</th>
                            <td colspan="3" id="country"></td>
                        </tr>

                        <tr>
                            <th>City/Town/District</th>
                            <td colspan="3" id="city"></td>
                        </tr>

                        <tr>
                            <th>Provinciality/Municipality</th>
                            <td colspan="3" id="province"></td>
                        </tr>

                        <tr>
                            <th>Address</th>
                            <td colspan="3" id="address"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/order_details.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/libToggle.js') }}"></script>
@endpush