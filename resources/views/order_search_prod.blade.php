@extends('layout.popup')
@section('content')
<div class="main-content-container container-fluid px-4">
    <div class="row">
        <div class="col-lg-12 col-sm-12">
        <h4 class="mb-2 mt-2">Search Product</h4>        
            {{ csrf_field() }}
            <table class="table table-bordered mOption" border="1" summary>
                <tbody border="1">
                    <colgroup>
                        <col style="width: 10%">
                        <col style="width: 90%">
                    </colgroup>

                    <tr>
                        <th>Filter</th>
                        <td>
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control form-control-sm" id="select_product_filter" style="width: 100%">
                                        <option value="product_name" selected>Product Name</option>
                                        <option value="product_code">Product Code</option>
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <input class="form-control form-control-sm fText" type="text" id="input_filter_value" style="width: 100%" >
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 text-center">
            <a href="#" class="btn btn-primary" id="btnSearch">Search Product</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-sm-12 mt-3"> 
            <table class="table table-bordered" border="1" id=product_table>
                <thead class=" text-center">
                    <th class="text-center" width="10%;">Product Code</th>
                    <th class="text-center">Product Name</th>
                    <th class="text-center" width="20%">Bundle Product</th>
                    <th class="text-center" width="10%">Options</th>
                    <th class="text-center" width="10%">Select</th>
                </thead>
                <tbody class="text-center">
                    <tr id="product_template" hidden>
                        <td class="code"></td>
                        <td class="name"></td>
                        <td class="bundle"></td>
                        <td></td>
                        <td class="selected_product"></td>
                    </tr>
                </tbody>
            </table>

            <ul class="pagination justify-content-center"></ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.1/jquery.twbsPagination.min.js"></script>
<script type="text/javascript" src="{{ asset('js/search-product.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pagination.js') }}"></script>
@endpush