<?php
$pagename="contact";
?>
@include('layouts.header')


<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto ml-2">
        User's Contact List
    </h2>
</div>
<!-- BEGIN: Datatable -->
<div class="intro-y datatable-wrapper box p-5 mt-5">
    <table class="table table-report table-report--bordered display datatable w-full">
        <thead>
            <tr>
                <th class="border-b-2  whitespace-no-wrap">
                    Sr.</th>

                <th class="border-b-2  whitespace-no-wrap">
                    Name*</th>

                <th class="border-b-2  whitespace-no-wrap">
                    Email*</th>

                <th class="border-b-2  whitespace-no-wrap">
                    Phone*</th>

                <th class="border-b-2  whitespace-no-wrap">
                    Message*</th>

                <th class="border-b-2  whitespace-no-wrap">
                    Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php  $i = 0; ?>
            @foreach($contact as $cont)
            <?php $i++; ?>
            <tr>
                <th scope="row">{{ $i }}</th>
                <td>
                    <?= $cont->email?>
                </td>
              
                <td>
                    <?= $cont->email?>
                </td>
                <td>
                    <?= $cont->phone?>
                </td>
                <td>
                    <?= $cont->message?>
                </td>
                <td>
                    <button style="border:none;" type="button" value="{{$cont->id}}" class="deletebtn btn"><a
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
            <form type="submit" action="{{route('delete_contact')}}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="delete_contact_id" id="deleting_id"></input>
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