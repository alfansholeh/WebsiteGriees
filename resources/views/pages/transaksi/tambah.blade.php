@extends("layouts.auth")
@section("content")
    @include("components.popup")
    <script src="/js/addTransactionRow.js"></script>
    <div class="h-[calc(100vh-10%-1rem)] overflow-y-scroll bg-white p-4">
        <form action="/transaksi/tambah" id="form" method="post" class="w-full flex flex-col gap-4">
            @csrf
            <div class="flex items-center justify-between">
                <div class="flex flex-col gap-2">
                    <div>
                        <label for="tanggal">Tanggal : </label>
                        <input type="date" name="tanggal" id="tanggal" class="p-1"
                               value="{{old("tanggal") ?? date("Y-m-d")}}">
                    </div>
                    <div>
                        <label for="kategori_transaksi">Kategori Transaksi : </label>
                        <select name="kategori_transaksi" id="kategori_transaksi" class="p-1">
                            <option value="pemesanan" @if(old("kategori_transaksi") === "pemesanan") selected @endif>
                                Pemesanan
                            </option>
                            <option value="pembelian" @if(old("kategori_transaksi") === "pembelian") selected @endif>
                                Pembelian
                            </option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <h1>Total Keseluruhan</h1>
                    <h1 class="font-bold text-3xl">Rp.<span id="transaksi-total">0</span></h1>
                    {{--                    <input type="hidden" name="total" value="{{old("total")}}">--}}
                </div>
            </div>
            <div class="w-full border-2 border-gray-100">
                <div class="font-bold border-b-2 border-gray-100 flex w-full bg-primary text-white py-1">
                    <h2 class="border-e-2 text-center border-gray-100 w-1/3">Nama Produk</h2>
                    <h2 class="border-e-2 text-center border-gray-100 w-1/6">Jumlah Produk</h2>
                    <h2 class="border-e-2 text-center border-gray-100 w-1/6">Harga Satuan</h2>
                    <h2 class="border-e-2 text-center border-gray-100 w-1/6">Total</h2>
                    <h2 class="text-center border-gray-100 flex-grow">Hapus</h2>
                </div>
                <div id="listProduk" class="w-full">
                    {{--                    @if(old("$produk"))--}}
                    {{--                        @foreach(old("produk") as $i=>$produk)--}}
                    {{--                            <div id="${this.key}"--}}
                    {{--                                 class="flex items-center bg-slate-200 border-b-2 border-gray-100 bg-white">--}}
                    {{--                                <h2 class="border-e-2 text-center border-gray-100 w-1/3">${this.name}</h2>--}}
                    {{--                                <input type="hidden" name="produk[]" id="${this.key}-produk" value="{{$produk->id}}">--}}
                    {{--                                <input type="number" name="jumlah[]" id="${this.key}-jumlah" value="${this.jumlah}"--}}
                    {{--                                       min="${this.stok > 0 || " 0"}" max="${this.stok}"--}}
                    {{--                                onchange="Produk.total('${this.id}','${this.key}')" placeholder="0" class="p-0--}}
                    {{--                                border-e-2 text-center border-gray-100 w-1/6">--}}
                    {{--                                <h2 class="border-e-2 text-center border-gray-100 w-1/6">Rp.<span--}}
                    {{--                                        id="${this.key}-harga">${this.harga}</span></h2>--}}
                    {{--                                <h2 class="border-e-2 text-center border-gray-100 w-1/6">Rp.<span--}}
                    {{--                                        id="${this.key}-total">${this.harga * this.jumlah}</span></h2>--}}
                    {{--                                <button class="border-e-2 text-center border-gray-100 flex-grow material-icons"--}}
                    {{--                                        type="button" onClick="Produk.delete('${this.id}','${this.key}')">delete--}}
                    {{--                                </button>--}}
                    {{--                            </div>--}}
                    {{--                        @endforeach--}}
                    {{--                    @endif--}}
                </div>
                <div>
                    <select onchange="addTransactionRow()" class="w-1/3" id="addTransaction">
                        <option value="0" selected></option>
                        @foreach($produks as $produk)
                            <option class="produk" value="{{$produk->id+1}}" harga="{{$produk->harga}}"
                                    stok="{{$produk->stok}}">{{$produk->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex w-full gap-2">
                <div class="flex flex-col gap-2 w-1/4">
                    <div>
                        <label for="status_pembayaran">Status Pembayaran</label><br>
                        <select name="status_pembayaran" id="status_pembayaran" class="w-full p-1">
                            <option value="DP" id="DP" @if(old("status_pembayaran") === "DP") selected @endif>DP
                            </option>
                            <option value="Lunas" @if(old("status_pembayaran") === "Lunas") selected @endif>Lunas
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="metode">Metode Pembayaran</label><br>
                        <select name="metode" id="metode" class="w-full p-1">
                            <option value="BRI" @if(old("metode") === "BRI") selected @endif>BRI</option>
                            <option value="BNI" @if(old("metode") === "BNI") selected @endif>BNI</option>
                        </select>
                    </div>
                    <div>
                        <label for="jumlah_pembayaran">Jumlah Pembayaran</label><br>
                        <input type="number" name="jumlah_pembayaran" value="{{old("jumlah_pembayaran")}}"
                               class="p-1 w-full" id="jumlah_pembayaran">
                    </div>
                </div>
                <div class="flex-grow">
                    <label for="keterangan">Keterangan *</label><br>
                    <textarea name="keterangan" id="keterangan" cols="30" rows="7"
                              class="resize-none w-full">{{old("keterangan")}}</textarea>
                </div>
            </div>
            <button class="px-4 py-2 bg-slate-500 text-white rounded-lg">Cetak Invoice</button>
        </form>
    </div>
    <script>
        @if(old("produk"))
        @foreach(old("produk") as $i=>$p)
        addTransactionRow("{{(int)$p+1}}", "{{old("jumlah")[$i]}}");
        @endforeach
        @endif
        $("#form").submit(function (e) {
            e.preventDefault();
            if ($("#status_pembayaran").val() === "Lunas") {
                const bayar = $("#jumlah_pembayaran");
                if (parseInt(bayar.val()) >= total) {
                    this.submit();
                    return true;
                }
                return false;
            } else {
                this.submit();
                return true;
            }
        });
        $("#kategori_transaksi").change(function (e) {
            const state = $(this).val();
            if (state === "pembelian") {
                $("#DP").attr("disabled", true);
            }
        });
    </script>
@stop
