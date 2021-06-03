@extends('layout.default')

@section('content')
<div class="main-content-container container-fluid px-4">
    <div class="row mt-3">
        <div class="col-lg-12 col-sm-12">
            <h4 class="mb-2 mt-2">Order List</h4>
            {{ csrf_field() }}
            <table class="table table-bordered bg-gray">
                <tbody border="1">
                    <colgroup>
                        <col style="width: 10%">
                        <col style="width: 90%">
                    </colgroup>

                    <tr>
                        <th>Filter</th>
                        <td>
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-control form-control-sm" id="order_filter">
                                        <option value="order_id" {{ (isset($oParams['embed']['order_id']) === true) ? 'selected' : ''}}>Order ID</option>
                                        <option value="item_code" {{ (isset($oParams['embed']['item_code']) === true) ? 'selected' : ''}}>Item Code</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm fText" type="text" id="order_filter_value" style="width: 100%;" value="{{ isset($oParams['embed']['order_id']) === true ? $oParams['embed']['order_id'] : ((isset($oParams['embed']['item_code']) === true) ? $oParams['embed']['item_code'] : '')}}">
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th>Order Date Range</th>
                        <td class="date-options">
                            <a href="#" class="btn btn-sm btn-flat btn-secondary btnDate" id="day_0">Today</a>
                            <a href="#" class="btn btn-sm btn-flat btn-secondary btnDate" id="day_1">Yesterday</a>
                            <a href="#" class="btn btn-sm btn-flat btn-secondary btnDate" id="day_3">3 days</a>
                            <a href="#" class="btn btn-sm btn-flat btn-secondary btnDate" id="day_7">7 days</a>
                            <a href="#" class="btn btn-sm btn-flat btn-secondary btnDate" id="day_15">15 days</a>
                            <a href="#" class="btn btn-sm btn-flat btn-secondary btnDate" id="month_1">1 month</a>
                            <a href="#" class="btn btn-sm btn-flat btn-primary btnDate" id="month_3">3 months</a>

                            <input type="text" class="form-control form-control-sm d-inline-block fText text-center" id="start" readonly style="width: 200px;" value="{{ isset($oParams['start_date']) === true ? $oParams['start_date'] : ''}}">
                            <a href="javascript:" id="start_date_cal" class="btn btn-sm btn-secondary" data-date-format="yyyy-mm-dd"><i class="fa fa-calendar"></i></a> ~

                            <input type="text" class="form-control form-control-sm d-inline-block align-middle fText text-center" readonly id="end" style="width: 200px;" value="{{ isset($oParams['end_date']) === true ? $oParams['end_date'] : ''}}">
                            <a href="javascript:" id="end_date_cal" class="btn btn-sm btn-secondary" data-date-format="yyyy-mm-dd"><i class="fa fa-calendar"></i></a>
                        </td>
                    </tr>


                    <tr>
                        <th>Product</th>
                        <td>
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="" id="select_product_filter" class="form-control form-control-sm">
                                        <option value="product_code" {{ (isset($oParams['embed']['product_code']) === true) ? 'selected' : ''}}>Product Code</option>
                                        <option value="variant_code" {{ (isset($oParams['embed']['variant_code']) === true) ? 'selected' : ''}}>Variant Code</option>
                                    </select>
                                </div>

                                <div class="col-md-5 form-inline">
                                    <input type="text" class="form-control form-control-sm fText" id="input_product_value" style="width: 100%;" value="{{ (isset($oParams['embed']['product_code']) === true) ? $oParams['embed']['product_code'] : ((isset($oParams['embed']['variant_code']) === true) ? $oParams['embed']['variant_code'] : '') }}">

                                </div>

                                <a href="#" class="btn btn-secondary btn-sm" id="searchProduct">
                                    Search Product <i class="fa fa-play"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 text-center">
            <a href="#" class="btn btn-primary" id="btnSearchOrder">Search Order</a>
            <a href="#" class="btn btn-secondary" id="btnResetOrder">Reset</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-sm-12 mt-3" id="order_list">
            <table class="table table-bordered" id="tbl_order">
                <thead class="table-secondary text-center">
                    <th>Order ID</th>
                    <th>Member ID</th>
                    <th>Member E-mail</th>
                    <th>Order Status</th>
                </thead>
                <tbody class="text-center">
                @if (isset($oOrders) === false || count($oOrders) < 1)
                    <tr>
                        <td colspan="4">No available data</td>
                    </tr>
                @else
                    @foreach ($oOrders as $iKey => $aOrder)
                        <tr id="order-template">
                            <td class="order">
                                <a href="#" class="tr_order">{{ $aOrder['order_id'] }}</a>
                            </td>
                            <td class="member">{{ $aOrder['member_id']  }}</td>
                            <td class="email">{{ ($aOrder['member_email'] === '') ? $aOrder['buyer']['email'] : $aOrder['member_email'] }}</td>
                            <td class="status">{{ $aOrder['items'][0]['order_status'] }}</td>
                        </tr>
                    @endforeach
                @endif

                </tbody>
            </table>

            <ul class="pagination justify-content-center">
                @if (isset($oPaging) === true)  {!! $oPaging !!} @endif
            </ul>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/libDateUtil.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/order.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pagination.js') }}"></script>
@endpush
