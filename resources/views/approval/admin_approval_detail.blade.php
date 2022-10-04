@extends('layouts.admin.template_admin')
@section('content')
@php
$admin = \App\Models\User::where('id', Auth::user()->id)->where('role', 'admin')->first();
$user = \App\Models\User::where('id', Auth::user()->id)->where('role', null)->first();  
@endphp

@if($admin)
    <div class="row">
        <div class="col-9">
            <div class="card">
                            <div class="card mt-2">
                                <div class="card-body">
                                    <!-- Button trigger modal -->
                                    <div class="text-right">
                                    </div>

                                    <div class="table-responsive">
                                        @if($pengajuan->user_id_approval == null)
                                            <div class="card-body" id="show_data_kelas">
                                                <h1 class="text-center text-secondary my-5">Loading...</h1>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
        </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">Action</div>
                        <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                {{-- <p>total biaya : Rp {{ number_format($->barang->harga_barang,2,',','.') }}</p> --}}
                                
                        @if($pengajuan->user_id_approval == null)
                            @if($pengajuan_detail->count() > 0)
                            <p>Total Biaya</p>
                            <h1 class="dimmas" id="data">

                            </h1>
                            <p>Rp {{ number_format($pengajuan->total_biaya,2,',','.') }}</p>
                                <form action="/approval/approve_admin" method="post">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $pengajuan->id }}">
                                    <input type="submit" class="btn btn-success mb-5 text-center" value="Approve">
                                </form>
                                    
                            @endif
                        @endif
                            </div>
                        <div class="col-6">
                            <a href="/approval/admin" class="btn btn-primary">Back</a>
                           
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="pengajuan_detail" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Data Pengajuan</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button> --}}
            </div>
            <form action="#" method="POST" id="form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="pengajuan_id" id="pengajuan_id">
                <div class="modal-body p-4 bg-light">
                    <div class="row">
                        <div class="col-lg">
                            <label for="jumlah_barang">jumlah_barang</label>
                            <input type="number" name="jumlah_barang" id="jumlah_barang" class="form-control" placeholder="Jumlah barang" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="button" class="btn btn-success">Update</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    </div>

@endif

@section('script')

  <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.js"></script>
    
<script>

    
    $(function() {


 $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        $.ajax({
          url: '/approval/edit/ajax',
          method: 'get',
          data: {
            id: id,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            $("#jumlah_barang").val(response.jumlah_barang);
            $("#pengajuan_id").val(response.pengajuan_id);
            $("#id").val(response.id);
          }
        });
      });
      
    $("#form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#button").text('Updating...');
        $.ajax({
          url: '/approval/update/ajax',
          method: 'post',
          data: fd,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
            if (response.status == 200) {
              Swal.fire(
                'Updated!',
                'kelas Updated Successfully!',
                'success'
              )
              total();
              kelas_all();
              
            }
            $("#button").text('Update');
            $("#form")[0].reset();
            $("#pengajuan_detail").modal('hide');
          }
        });
      });

      kelas_all();

      function kelas_all() {
        $.ajax({
          url: '{{ route('all', $pengajuan->id) }}',
          method: 'get',
          success: function(response) {
            $("#show_data_kelas").html(response);
            $("table").DataTable({
                 "bFilter": false, 
                 "bInfo": false,
                 "bPaginate": false,
                 "paging": false,
              order: [0, 'desc']
            });
          }
        });
      }

       total();

        function total() {
        $.ajax({
          url: '{{ route('total', $pengajuan->id) }}',
          method: 'get',
          success: function(response) {
            // $("#total_biaya").html(response);
             $('#tes').html(response.tes);
             $('#data').html(response.data);
            //  $('.dimmas').html(response);
          }
        });
      }

    $( document ).ready(function() {
    $.ajax({
          url: '{{ route('total', $pengajuan->id) }}',
          method: 'get',
          success: function(response) {
            // $("#total_biaya").html(response);
            //  $('#tes').html(response.tes);
             $('#data').html(response.data);
            //  $('.dimmas').html(response);
          }
        });
});

//         $.get( "{{ route('total', $pengajuan->id) }}", function( data ) {
//         $("#result").html(data);
//          console.log(data);
//         alert( "Load was performed.");
//         });

//         $( document ).ready(function(dimmas) {
//     console.log( "ready!" );
    
//         $(".dimmas").html(dimmas);
// });

    });
</script>

@endsection
@endsection