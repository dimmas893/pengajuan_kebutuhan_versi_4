<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Pengajuan_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $belum_approve = Pengajuan::with('user_pengajuan')->where('user_id_approval', null)->where('user_id_pengajuan' , Auth::user()->id)->get();
        $approve = Pengajuan::with('user_pengajuan')->where('user_id_approval','!=', null)->where('user_id_pengajuan', Auth::user()->id)->get();
        return view('approval.index', compact('belum_approve', 'approve'));
    }

    public function detail($id)
    {
        $pengajuan_detail = Pengajuan_detail::with('barang')->where('pengajuan_id', $id)->get();
        $pengajuan = Pengajuan::where('id', $id)->first();
        return view('approval.detail', compact('pengajuan_detail', 'pengajuan'));
    }

    public function edit($pengajuan_detail, $pengajuan)
    {
        $pengajuan_detail = Pengajuan_detail::with('barang')->where('pengajuan_id', $pengajuan)->Find($pengajuan_detail);
        return view('approval.edit', compact('pengajuan_detail'));
    }

    public function update(Request $request)
    {



        $update = Pengajuan_detail::where('id', $request->id)->first();
        $update->jumlah_barang = $request->jumlah_barang;
        $update->save();



        $pengajuan_detail = Pengajuan_detail::where('pengajuan_id', $request->pengajuan_id)->get();
        // return response()->json([
        //     'data' => $pengajuan_detail
        // ]);
        $jsonNilai = array();
        foreach ($pengajuan_detail as $p) {
            $row =  $p->jumlah_barang * $p->harga_satuan;
            array_push($jsonNilai, $row);
        }
        // dd($jsonNilai);

        Pengajuan::where('id', $request->pengajuan_id)->update([
            'total_biaya' => array_sum($jsonNilai),
        ]);
        return back();
    }

    public function admin()
    {

        $belum_approve = Pengajuan::with('user_pengajuan')->where('user_id_approval', null)->get();
        $approve = Pengajuan::with('user_pengajuan')->where('user_id_approval', '!=', null)->get();
        return view('approval.admin_approval', compact('belum_approve', 'approve'));
    }

    public function approve_admin(Request $request)
    {
        $pengajuan = Pengajuan::where('id', $request->id)->first();
        $pengajuan->user_id_approval = 1;
        $pengajuan->update();
        return back();
    }

    public function approve_admin_detail($id)
    {
        $pengajuan_detail = Pengajuan_detail::with('barang')->where('pengajuan_id', $id)->get();
        $pengajuan = Pengajuan::where('id', $id)->first();

        return view('approval.admin_approval_detail', compact('pengajuan_detail', 'pengajuan'));
    }

    public function editaprrove(Request $request)
    {
        $id = $request->id;
        $emp = Pengajuan_detail::where('id', $id)->first();
        return response()->json($emp);
    }

    public function updateapprove(Request $request)
    {

        // dd(request());
        $empData = [
            'jumlah_barang' => $request->jumlah_barang,
        ];
        $emp = Pengajuan_detail::where('id', $request->id)->update($empData);

        $pengajuan_detail = Pengajuan_detail::where('pengajuan_id', $request->pengajuan_id)->get();

        $jsonNilai = array();
        foreach ($pengajuan_detail as $p) {
            $row =  $p->jumlah_barang * $p->harga_satuan;
            array_push($jsonNilai, $row);
        }
        // $emp->update($empData);
        Pengajuan::where('id', $request->pengajuan_id)->update([
            'total_biaya' => array_sum($jsonNilai),
        ]);

        // dd($emp);
        return response()->json([
            'status' => 200,
        ]);
    }

    public function total($id)
    {
        $pengajuan_detail = Pengajuan_detail::where('pengajuan_id', $id)->get();

        $jsonNilai = array();
        foreach ($pengajuan_detail as $p) {
            $row =  $p->jumlah_barang * $p->harga_satuan;
            array_push($jsonNilai, $row);
        }

        return response()->json([
            // array_sum($jsonNilai)
            'data' => array_sum($jsonNilai),
            'tes' => 44
        ]);
    }

    public function delete($id, Request $request)
    {
        $pengajuan_detail = Pengajuan_detail::Find($id)->delete();

        $pengajuan_detail = Pengajuan_detail::where('pengajuan_id', $request->pengajuan_id)->get();

        $jsonNilai = array();
        foreach ($pengajuan_detail as $p) {
            $row =  $p->jumlah_barang * $p->harga_satuan;
            array_push($jsonNilai, $row);
        }
        // dd($jsonNilai);

        Pengajuan::where('id', $request->pengajuan_id)->update([
            'total_biaya' => array_sum($jsonNilai),
        ]);
        return back();
    }

    public function approve_admin_edit($pengajuan_detail, $pengajuan)
    {
        $pengajuan_detail = Pengajuan_detail::with('barang')->where('pengajuan_id', $pengajuan)->Find($pengajuan_detail);
        return view('approval.admin_approval_edit', compact('pengajuan_detail'));
    }

    public function approve_admin_update(Request $request)
    {



        $update = Pengajuan_detail::where('id', $request->id)->first();
        $update->jumlah_barang = $request->jumlah_barang;
        $update->save();



        $pengajuan_detail = Pengajuan_detail::where('pengajuan_id', $request->pengajuan_id)->get();
        // return response()->json([
        //     'data' => $pengajuan_detail
        // ]);
        $jsonNilai = array();
        foreach ($pengajuan_detail as $p) {
            $row =  $p->jumlah_barang * $p->harga_satuan;
            array_push($jsonNilai, $row);
        }
        // dd($jsonNilai);

        $total = Pengajuan::where('id', $request->pengajuan_id)->update([
            'total_biaya' => array_sum($jsonNilai),
        ]);
        return back();
    }

    public function delete_pengajuan($id)
    {
        $pengajuan = Pengajuan::Find($id)->delete();
        return back();
    }

    public function all($id)
    {

        $emps = Pengajuan_detail::with('barang')->where('pengajuan_id', $id)->get();
        $output = '';
        if ($emps->count() > 0) {
            $output .=
                '<table class="table table-striped table-sm text-center align-middle">
             <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Spesifikasi Barang</th>
                <th>jumlah Barang</th>
                <th>Harga Satuan Barang</th>
                <th>edit</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($emps as $emp) {
                $output .= '<tr>
                <td>' . $emp->barang->nama_barang . '</td>
                <td>' . $emp->barang->spesifikasi . '</td>
                <td>' . $emp->jumlah_barang . '</td>
                <td>' . 'Rp' . '  ' . number_format($emp->harga_satuan, 2, ',', '.') . '</td>
                <td>
                  <a href="#" id="' . $emp->id . '" class="btn btn-primary mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#pengajuan_detail">edit</a>

                  <a href="#" id="' . $emp->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }
    }
    
}
