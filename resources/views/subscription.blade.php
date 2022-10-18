<?php
$pagename="subscription";
?>
@include('layouts.header')


<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto ml-2">
        Subscription History
    </h2>

    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button style="border:none;" type="button" class="button text-white bg-theme-42 shadow-md mr-2"><a
                href="{{route('lucky_draw')}}">Lucky Draw</a>
        </button>
    </div>

    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button style="border:none;" type="button" class="button text-white bg-theme-42 shadow-md mr-2">
            <select name="status" class=" border-0 bg-theme-42 shadow-md mr-2 " name="month" id="" class="category"
                aria-label value="" onchange="window.location.href='/subscription_by_month/'+this.value">
                <option value="0">All Months</option>
                <option value="1" <?php echo ( $month==1) ? 'selected' : "" ; ?> >January</option>
                <option value="2" <?php echo ( $month==2) ? 'selected' : "" ; ?>>February</option>
                <option value="3" <?php echo ( $month==3) ? 'selected' : "" ; ?>>March</option>
                <option value="4" <?php echo ( $month==4) ? 'selected' : "" ; ?>>April</option>
                <option value="5" <?php echo ( $month==5) ? 'selected' : "" ; ?>>May</option>
                <option value="6" <?php echo ( $month==6) ? 'selected' : "" ; ?>>June</option>
                <option value="7" <?php echo ( $month==7) ? 'selected' : "" ; ?>>July</option>
                <option value="8" <?php echo ( $month==8) ? 'selected' : "" ; ?>>August</option>
                <option value="9" <?php echo ( $month==9) ? 'selected' : "" ; ?>>September</option>
                <option value="10" <?php echo ( $month==10) ? 'selected' : "" ; ?>>October</option>
                <option value="11" <?php echo ( $month==11) ? 'selected' : "" ; ?>>November</option>
                <option value="12" <?php echo ( $month==12) ? 'selected' : "" ; ?>>December</option>
            </select>
        </button>
    </div>
</div>
<!-- BEGIN: Datatable -->
<div class="intro-y datatable-wrapper box p-5 mt-5">
    <table class="table table-report table-report--bordered display datatable w-full">
        <thead>
            <tr>
                <th class="border-b-2  whitespace-no-wrap">
                    Sr.</th>

                <th class="border-b-2  whitespace-no-wrap">
                    Profile Image*</th>
                <th class="border-b-2  whitespace-no-wrap">
                    User Name*</th>
                <th class="border-b-2  whitespace-no-wrap">
                    Email*</th>
                <th class="border-b-2  whitespace-no-wrap">
                    Phone Number*</th>
                <th class="border-b-2  whitespace-no-wrap">
                    Amount $</th>
                <th class="border-b-2  whitespace-no-wrap">
                    Date*</th>
                <th class="border-b-2  whitespace-no-wrap">
                    Status*</th>
                <th class="border-b-2  whitespace-no-wrap">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php  $i = 0; ?>
            @foreach($subscription as $subs)
            <?php $i++; ?>
            <tr>
                <th scope="row">{{ $i }}</th>
                <td>
                    <img src="{{asset('public/storage/'. $subs->profile_img)}}" width="50" height="50">
                </td>
                <td>
                    <?= $subs->first_name?>
                </td>
                <td>
                    <?= $subs->email?>
                </td>
                <td>
                    <?= $subs->phone?>
                </td>
                <td>
                    <?= $subs->amount?> $
                </td>
                <td>
                    <?=  date('d-m-Y', strtotime($subs->date)); ?>
                </td>
                <td>
                    <?= $subs->status ?>
                </td>
                <td>
                    <button style="border:none;" type="button" value="{{$subs->id}}" class="deletebtn btn"><a
                            class=" flex items-center text-theme-6" href="javascript:;" data-toggle="modal"
                            data-target="#delete-modal-preview"> <i data-feather="trash-2" class="w-4 h-4 mr-1"></i>
                            Delete </a>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- END: Datatable -->
<div class="modal" id="delete-modal-preview">
    <div class="modal__content">
        <div class="p-5 text-center">
            <i data-feather="x-circle" class="w-16 h-16 text-theme-6 mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-gray-600 mt-2">Do you really want to delete these records? This process cannot be
                undone.
            </div>
        </div>
        <div class="px-5 pb-8 text-center">
            <form type="submit" action="{{route('delete_subscription')}}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="delete_subs_id" id="deleting_id"></input>
                <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 mr-1">Cancel</button>
                <button type="submit" class="button w-24 bg-theme-6 text-white p-3 pl-5 pr-5">Delete</button>
            </form>
        </div>
    </div>
</div>
<script>

    $(document).on('click', '.deletebtn', function () {
        var user_id = $(this).val();
        $('#deleting_id').val(user_id);
    });


</script>
@include('layouts.footer')