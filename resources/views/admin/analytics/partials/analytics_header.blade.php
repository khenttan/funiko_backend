<style>
    .small-box h3, .small-box p {
        z-index: 9999999999;
    }

    .small-charts {
        width: 90px !important;
        height: 60px;
    }

    .small-box .icon-chart {
        position: absolute;
        top: 15px;
        right: 5px;
        z-index: 0;
    }
</style>

<div class="row">
    <div class="col-lg col-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3 id="visitors">{{ $user_count == 0 ? 0 :  $user_count - 1  }}</h3>
                <p>Total Number of registrations</p>
                <div class="icon-chart">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg col-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 id="unique-visitors">{{ $post_count }}
                </h3>
                <p>Total number of posts</p>

                <div class="icon-chart">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3 id="bounce-rate">{{ $group_count }}</h3>
                <p>Total number of groups</p>

                <div class="icon-chart">
                </div>
            </div>
        </div>
    </div>
</div>
<form action="" id="dashboard_form" method="post">
    @csrf
<div class="row">
    <div class="col col-3">
        <div class="form-group">
            <label for="active_from">Active from
                <span class="small">(Required)</span></label>
            <div class="input-group"> 
                <input type="text" class="form-control {{ form_error_class('active_from', $errors) }}"   id="active_from" name="active_from" data-date-format="YYYY-MM-DD" placeholder="Enter Active From" value="{{ ($errors && $errors->any()? old('active_from') : (isset($old_inputs)? $old_inputs['active_from'] : '')) }}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                </div>
                {!! form_error_message('active_from', $errors) !!}
            </div>
        </div>
    </div>

    <div class="col col-3">
        <div class="form-group">
            <label for="active_to">Active To
                <span class="small">(Required)</span></label>
            <div class="input-group">
                <input type="text" class="form-control {{ form_error_class('active_to', $errors) }}" id="active_to" name="active_to" data-date-format="YYYY-MM-DD" placeholder="Enter Active From" value="{{ ($errors && $errors->any()? old('active_to') : (isset($old_inputs)? $old_inputs['active_to'] : '')) }}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                </div>
                {!! form_error_message('active_to', $errors) !!}
            </div>
        </div>
    </div>
    <div class="col col-3">
        <div class="form-group">
            <label for="active_from">Gender
                <span class="small">(Optional)</span></label>
            <div class="input-group">
               <select name="gender" class="form-control"    >
                <option selected disabled> Please Select</option>
                <option {{ isset($old_inputs)  && isset($old_inputs['gender']) && $old_inputs['gender'] == "male" ? "selected" : "" }} value="male"> Male</option>
                <option {{ isset($old_inputs)  && isset($old_inputs['gender']) && $old_inputs['gender'] == "female" ? "selected" : "" }}  value="female"> Female</option>
               </select>
            </div>
        </div>
    </div>
    <div class="col col-3">
        <div class="form-group">
            <label for="active_from">
            </label>
            <button class="btn btn-primary form-control" type="submit">Submit</button>
        </div>
    </div>
</div>
</form>


@section('scripts')
    @parent
    <script type="text/javascript" charset="utf-8">
        $(function () {
            setDateTimePickerRange('#active_from', '#active_to');
        })
    </script>
@endsection 



