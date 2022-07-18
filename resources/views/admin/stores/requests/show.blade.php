@extends('admin.admin')

@section('content')
    <div class="card  card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <span><i class="fa fa-eye"></i></span>
                <span>Store - {{ $item->shop_name }}</span>
            </h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <form>
            <div class="card-body">

                @include('admin.partials.card.info')


                <fieldset>
                    <div class="row">
                        <section class="col col-6">
                            <section class="form-group">
                                <label>Seller Name</label>
                                <input type="text" class="form-control" value="{{ userDetailsById($item->user_id)??''}}" readonly>
                            </section>
                        </section>

                        <section class="col col-6">
                            <section class="form-group">
                                <label>Bio</label>
                                <input type="text" class="form-control" value="{{$item->shop_description??''}}" readonly>
                            </section>
                        </section>
                    </div>
                    <div class="row">
                        <section class="col col-6">
                            <section class="form-group">
                                <label>Store Address</label>
                                <input type="text" class="form-control" value="{{ $item->shop_address??''}}" readonly>
                            </section>
                        </section>

                        <section class="col col-6">
                            <section class="form-group">
                                <label>Mobile</label>
                                <input type="text" class="form-control" value="{{$item->shop_mobile??''}}" readonly>
                            </section>
                        </section>
                    </div>

                    @if(isset($item) && $item && $item->shop_photo)
                        <section>
                            <img src="{{ uploaded_images_url($item->shop_photo) }}" style="max-height: 300px;">
                            <input type="hidden" name="image" value="{{ $item->shop_photo }}">
                        </section>
                    @endif
                </fieldset>


            </div>
            @include('admin.partials.form.form_footer', ['submit' => false])
        </form>
    </div>
@endsection
