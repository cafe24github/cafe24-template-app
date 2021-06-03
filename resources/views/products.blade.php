@extends('layout.default')

@section('content')
<input type="hidden" id="txtLimit" value="{{ config('app.banner_limit') }}">
<div class="main-content-container container-fluid px-4">
    <div class="row mt-3">
        <div class="col-lg-12 col-sm-12">
            <h4 class="mb-2 mt-2">Products List</h4>
        </div>
        <div class="col-lg-12">
            <table class="table table-bordered bg-gray">
                <tbody>
                <tr>
                    <th class="w-15">Filter</th>
                    <td>
                        <div class="row">
                            <div class="col-lg-2">
                                <select class="form-control filter" id="selClassification" disabled>
                                    <option {{ isset($oParams['product_name']) === true ? 'selected' : ''  }} value="product_name"> Product Name </option>
                                    <option {{ isset($oParams['product_code']) === true ? 'selected' : ''  }} value="product_code"> Product Code </option>
                                </select>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" class="form-control w-75 filter" id="txtClassification" disabled value="{{ isset($oParams['product_name']) === true ? $oParams['product_name'] : (isset($oParams['product_code']) === true ? $oParams['product_code'] : '' ) }}">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-15">Product Category</th>
                    <td>
                        <div class="row">
                            <div class="col-lg-2">
                                <select id="selSubCategory1" class="selCategory form-control filter" disabled data-depth="1">
                                    <option value="0" selected> Main Category </option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select id="selSubCategory2" class="selCategory form-control filter" disabled data-depth="2">
                                    <option value="0" selected> Sub Category 1 </option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select id="selSubCategory3" class="selCategory form-control filter" disabled data-depth="3">
                                    <option value="0" selected> Sub Category 2 </option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select id="selSubCategory4" class="selCategory form-control filter" disabled data-depth="4">
                                    <option value="0" selected> Sub Category 3 </option>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-15">Display status</th>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input filter disabled" type="radio" name="radDisplayStatus" id="radDisplayAll" value="A" disabled {{ isset($oParams['display']) === false ? 'checked' : '' }}>
                            <label class="form-check-label" for="radDisplayAll">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input filter disabled" type="radio" name="radDisplayStatus" id="radDisplayShowcase" disabled value="T" {{ isset($oParams['display']) === true && $oParams['display'] === 'T' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radDisplayShowcase">Showcase</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input filter disabled" type="radio" name="radDisplayStatus" id="radDontDisplay" disabled value="F" {{ isset($oParams['display']) === true && $oParams['display'] === 'F' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radDontDisplay">Do not display</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-15">Selling status</th>
                    <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input filter" type="radio" name="radSellingStatus" id="radSellingAll" disabled value="A" {{ isset($oParams['selling']) === false ? 'checked' : '' }}>
                            <label class="form-check-label" for="radSellingAll">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input filter" type="radio" name="radSellingStatus" id="radSellingSold" disabled value="T" {{ isset($oParams['selling']) === true && $oParams['selling'] === 'T' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radSellingSold">Sold</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input filter" type="radio" name="radSellingStatus" id="radSellingDontSell" disabled value="F" {{ isset($oParams['selling']) === true && $oParams['selling'] === 'F' ? 'checked' : '' }}>
                            <label class="form-check-label" for="radSellingDontSell">Do not sell</label>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 text-center">
            <a type="button" class="btn btn-primary disabled" id="btnSearch">Search Product</a>
            <a type="button" class="btn btn-secondary" id="" href="{{ url('/products') }}">Reset</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <h4>Product List</h4>
        </div>
        <div class="col-lg-6 text-right">
            @if($iCount < 2)
                <h6>Total of {{ $iCount }} Item</h6>
            @else
                <h6>Total of {{ $iCount }} Items</h6>
            @endif
        </div>
        <div class="col-lg-12">
            <table class="table table-bordered" id="tblProduct">
                <thead class="thead-light text-center">
                    <th scope="col" style="width: 2%;">No</th>
                    <th scope="col" style="width: 30%;">Product</th>
                    <th scope="col" style="width: 20%;">Description</th>
                    <th scope="col" style="width: 15%;">Price</th>
                    <th scope="col" style="width: 15%;">Quantity</th>
                    <th scope="col" style="width: 10%;">Action</th>
                </thead>
                <tbody>
                @if (count($aProduct['products']) < 1)
                    <tbody class="empty text-center">
                    <tr>
                        <td colspan="6"> No Available Data </td>
                    </tr>
                    </tbody>
                @else
                    @foreach ($aProduct['products'] as $iIndex => $oProduct)
                        <tr>
                            <th scope="row">{{ $iOffset + $iIndex + 1 }}</th>
                            <td>
                                <div class="row">
                                    <div class="col-lg-2 pr-0 text-center my-auto">
                                               <span class="frame">
                                                  <img src="{{ $oProduct['tiny_image'] === null ? $oDefaultImage : $oProduct['tiny_image'] }}"  width="48" height="48"  alt="Product image">
                                               </span>
                                           </div>
                                           <div class="col-lg-9 pl-0">
                                               <h5 class="text-name mb-0">{{ $oProduct['product_name'] }}</h5>
                                               <h6 class="text-code">{{ $oProduct['product_code'] }}</h6>
                                           </div>
                                       </div>
                                   </td>
                                   <td>
                                       {{ $oProduct['summary_description'] === '' ? 'N/A' : $oProduct['summary_description']}}
                                   </td>
                                   <td>{{ number_format($oProduct['price'], 2, '.', ',') }}</td>
                                   <td>{{ $oProduct['options'] !== null ? count($oProduct['options']['options']) : 0 }}</td>
                                   <td>
                                       @if (in_array($oProduct['product_no'], $aSavedProduct) === true)
                                           <a type="button" class="btn btn-primary deleteProduct" data-id="{{ $oProduct['product_no'] }}"><i class="fa fa-eye"></i></a>
                                       @else
                                           <a type="button" class="btn btn-secondary saveProduct {{ $iCountSavedProduct >= config('app.banner_limit') ? 'disabled' : '' }}" data-id="{{ $oProduct['product_no'] }}"><i class="fa fa-eye-slash"></i></a>
                                       @endif
                                   </td>
                               </tr>
                       @endforeach
                    @endif
               </tbody>
           </table>
       </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {!! $oPaging !!}
        </div>
    </div>
    <div id="divTemplates" class="d-none">
        <select id="selCategoryTemplate">
        </select>

        <select id="selCategoryOptionTemplate">
            <option value="0"> </option>
        </select>

        <div id="btnDeleteTemplate">
            <a type="button" class="btn btn-primary deleteProduct" ><i class="fa fa-eye"></i></a>
        </div>

        <div id="btnSaveTemplate">
            <a type="button" class="btn btn-secondary saveProduct" ><i class="fa fa-eye-slash"></i></a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/libConstant.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/libToggle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/product.js') }}"></script>
@endpush
