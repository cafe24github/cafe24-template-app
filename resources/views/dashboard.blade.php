@extends ('layout.default')

@section ('content')
<div class="main-content-container container-fluid px-4">
    <div class="row mt-3">
        <div class="col-lg-12 col-sm-12">
            <h4 class="mb-2 mt-2">Marked Product List</h4>
        </div>
        <div class="col-lg-6 text-right">

        </div>
        <div class="col-lg-12">
            <table class="table table-bordered">
                <thead class="thead-light text-center">
                    <th scope="col" style="width: 2%;">No</th>
                    <th scope="col" style="width: 30%;">Product</th>
                    <th scope="col" style="width: 20%;">Description</th>
                    <th scope="col" style="width: 15%;">Price</th>
                </thead>
                <tbody>
                @if (count($aData) < 1)
                    <tbody class="empty text-center">
                    <tr>
                        <td colspan="6"> No Available Data </td>
                    </tr>
                    </tbody>
                @else
                    @foreach ($aData as $index => $oProduct)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>
                                <div class="row">
                                    <div class="col-lg-2 pr-0 text-center my-auto">
                                               <span class="frame">
                                                  <img src="{{ $oProduct['tiny_image'] === null ? $oProduct['default_image'] : $oProduct['tiny_image'] }}"  width="48" height="48"  alt="Product image">
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
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

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
