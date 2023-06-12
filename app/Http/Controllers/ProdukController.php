<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Varian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;

class ProdukController extends Controller
{
    public function index()
    {
        $form = false;
        $produks = Produk::where("usaha_id", Auth::user()->usaha->id)->get();
        return view("pages.produk.index", compact("produks", "form"));
    }

    public function tambahProdukForm()
    {
        $form = true;
        $produks = Produk::where("usaha_id", Auth::user()->usaha_id)->get();
        return view("pages.produk.tambah", compact("produks", "form"));
    }

    public function tambahProduk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama" => "required|regex:/^[\pL\s\-]+$/u",
            "ukuran" => "required",
            "harga" => "required|integer",
            "gambar" => "required",
            "deskripsi" => "required",
            "varian" => "required"
        ]);
        if($validator->fails()){
            if($this->isError($validator->messages()->get("nama"), "The nama format is invalid.")){
                return back()->with("message", "Format data tidak valid, Data nama produk hanya boleh berisi huruf");
            }
            return back()->with("message", "Ada data yang belum terisi");
        }
        $data = $validator->safe()->except("varian");
        $varian = $validator->safe()->only("varian");
        $data["stok"] = 0;
        DB::beginTransaction();
        try {
            $varianM = Varian::firstOrCreate(["nama" => $varian["varian"]]);
            $varianM->save();
            $file = $request->file("gambar");
            $data["gambar"] = Storage::put("pic/produk", $file);
            $data["varian_id"] = $varianM->id;
            $data["usaha_id"] = Auth::user()->usaha->id;
            $produk = new Produk($data);
            $produk->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return redirect("/produk")->with("message", "Data berhasil Disimpan");
    }

    public function ubahProdukForm($id)
    {
        $produk = Produk::find($id);
        return view("pages.produk.edit", compact("produk"));
    }

    public function ubahProduk($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nama" => "required|alpha",
            "ukuran" => "required",
            "harga" => "required|integer",
            "gambar" => "nullable",
            "deskripsi" => "required",
            "varian" => "required"
        ]);
        if($validator->fails()){
            if($this->isError($validator->messages()->get("nama"), "letters")){
                return back()->with("message", "Format data tidak valid, Data nama produk hanya boleh berisi huruf");
            }
            return back()->with("message", "Ada data yang belum terisi");
        }
        $data = $validator->safe()->only([
            "nama", "ukuran", "harga", "gambar", "deskripsi"
        ]);
        $varian = $validator->safe()->only([
            "varian"
        ]);
        $data["stok"] = 0;
        DB::beginTransaction();
        try {
            $varianM = Varian::firstOrCreate(["nama" => $varian["varian"]]);
            $varianM->save();
            $file = $request->file("gambar");
            if ($file) {
                $data["gambar"] = Storage::put("pic/produk", $file);
            }
            $data["varian_id"] = $varianM->id;
            $produk = Produk::find($id);
            $produk->update($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return redirect("/produk")->with("message", "Perubahan data berhasil Disimpan");
    }

    public function getDataProduk($id)
    {
        $produk = Produk::find($id);
        return view("pages.produk.index");
    }

    public function getpic($name)
    {
        if (Storage::has("pic/produk/" . $name)) {
            return response()->file(storage_path("app/pic/produk/" . $name));
        }
        abort(404);
    }
}
