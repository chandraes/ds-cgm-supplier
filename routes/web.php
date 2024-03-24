<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login')->with('status', 'Please login to continue.');
});

Auth::routes([
    'register' => false,
]);

Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::group(['middleware' => ['role:su,admin']], function() {
        // ROUTE PENGATURAN
        // Route::view('pengaturan', 'pengaturan.index')->name('pengaturan');
        Route::prefix('pengaturan')->group(function () {
            Route::get('/', [App\Http\Controllers\PengaturanController::class, 'index_view'])->name('pengaturan');
            Route::get('/wa', [App\Http\Controllers\WaController::class, 'index'])->name('pengaturan.wa');
            Route::get('/wa/get-wa-group', [App\Http\Controllers\WaController::class, 'get_group_wa'])->name('pengaturan.wa.get-group-wa');
            Route::patch('/wa/{group_wa}/update', [App\Http\Controllers\WaController::class, 'update'])->name('pengaturan.wa.update');

            Route::get('/akun', [App\Http\Controllers\PengaturanController::class, 'index'])->name('pengaturan.akun');
            Route::post('/akun/store', [App\Http\Controllers\PengaturanController::class, 'store'])->name('pengaturan.akun.store');
            Route::patch('/akun/{akun}/update', [App\Http\Controllers\PengaturanController::class, 'update'])->name('pengaturan.akun.update');
            Route::delete('/akun/{akun}/delete', [App\Http\Controllers\PengaturanController::class, 'destroy'])->name('pengaturan.akun.delete');

            Route::post('/password-konfirmasi', [App\Http\Controllers\PengaturanController::class, 'password_konfirmasi'])->name('pengaturan.password-konfirmasi');
            Route::post('/password-konfirmasi/cek', [App\Http\Controllers\PengaturanController::class, 'password_konfirmasi_cek'])->name('pengaturan.password-konfirmasi-cek');
        });

        Route::get('/histori-pesan', [App\Http\Controllers\HistoriController::class, 'index'])->name('histori-pesan');
        Route::post('/histori-pesan/resend/{pesanWa}', [App\Http\Controllers\HistoriController::class, 'resend'])->name('histori.resend');
        Route::delete('/histori-pesan/delete-sended', [App\Http\Controllers\HistoriController::class, 'delete_sended'])->name('histori.delete-sended');
        // END ROUTE PENGATURAN
    });

        // ROUTE DB
    Route::view('db', 'db.index')->name('db')->middleware('role:su,admin,user');
    Route::prefix('db')->group(function () {

        Route::group(['middleware' => ['role:su,admin,user']], function() {
            Route::get('/customer', [App\Http\Controllers\CustomerController::class, 'index'])->name('db.customer');
            Route::patch('/customer/{customer}/update-harga', [App\Http\Controllers\CustomerController::class, 'update_harga'])->name('db.customer.update-harga');
        });

        Route::group(['middleware' => ['role:su,admin']], function() {
            Route::post('/customer/store', [App\Http\Controllers\CustomerController::class, 'store'])->name('db.customer.store');
            Route::patch('/customer/{customer}/update', [App\Http\Controllers\CustomerController::class, 'update'])->name('db.customer.update');
            Route::delete('/customer/{customer}/delete', [App\Http\Controllers\CustomerController::class, 'destroy'])->name('db.customer.delete');

            Route::prefix('project')->group(function(){
                Route::get('/', [App\Http\Controllers\ProjectController::class, 'index'])->name('db.project');
                Route::post('/store', [App\Http\Controllers\ProjectController::class, 'store'])->name('db.project.store');
                Route::patch('/{project}/update', [App\Http\Controllers\ProjectController::class, 'update'])->name('db.project.update');
                Route::delete('/{project}/delete', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('db.project.delete');
            });

            Route::get('/investor', [App\Http\Controllers\InvestorController::class, 'index'])->name('db.investor');
            Route::patch('/investor/{investor}/update', [App\Http\Controllers\InvestorController::class, 'update'])->name('db.investor.update');

            Route::get('/rekening', [App\Http\Controllers\RekeningController::class, 'index'])->name('db.rekening');
            Route::patch('/rekening/{rekening}/update', [App\Http\Controllers\RekeningController::class, 'update'])->name('db.rekening.update');

            Route::prefix('investor-modal')->group(function (){
                Route::get('/', [App\Http\Controllers\InvestorModalController::class, 'index'])->name('db.investor-modal');
                Route::post('/store', [App\Http\Controllers\InvestorModalController::class, 'store'])->name('db.investor-modal.store');
                Route::patch('/{investor}/update', [App\Http\Controllers\InvestorModalController::class, 'update'])->name('db.investor-modal.update');
                Route::delete('/{investor}/delete', [App\Http\Controllers\InvestorModalController::class, 'destroy'])->name('db.investor-modal.delete');
            });
        });
    });


    Route::group(['middleware' => ['role:su,admin,user,investor']], function() {
        Route::get('rekap', [App\Http\Controllers\RekapController::class, 'index'])->name('rekap');
        Route::prefix('rekap')->group(function() {
            Route::get('/kas-besar', [App\Http\Controllers\RekapController::class, 'kas_besar'])->name('rekap.kas-besar');
            Route::get('/kas-besar/print/{bulan}/{tahun}', [App\Http\Controllers\RekapController::class, 'kas_besar_print'])->name('rekap.kas-besar.print');
            Route::get('/kas-besar/detail-tagihan/{invoice}', [App\Http\Controllers\RekapController::class, 'detail_tagihan'])->name('rekap.kas-besar.detail-tagihan');
            Route::get('/kas-besar/detail-bayar/{invoice}', [App\Http\Controllers\RekapController::class, 'detail_bayar'])->name('rekap.kas-besar.detail-bayar');

            Route::get('/kas-kecil', [App\Http\Controllers\RekapController::class, 'kas_kecil'])->name('rekap.kas-kecil');
            Route::get('/kas-kecil/print/{bulan}/{tahun}', [App\Http\Controllers\RekapController::class, 'kas_kecil_print'])->name('rekap.kas-kecil.print');
            Route::get('/kas-kecil/{kas}/void', [App\Http\Controllers\RekapController::class, 'void_kas_kecil'])->name('rekap.kas-kecil.void');

            Route::get('/invoice', [App\Http\Controllers\RekapController::class, 'rekap_invoice'])->name('rekap.invoice');
            Route::get('/invoice/detail-project', [App\Http\Controllers\RekapController::class, 'rekap_invoice_detail_project'])->name('rekap.invoice.detail-project');

            Route::get('/statistik/{customer}', [App\Http\Controllers\StatistikController::class, 'index'])->name('statistik.index');
            Route::get('/statistik/{customer}/print', [App\Http\Controllers\StatistikController::class, 'print'])->name('statistik.print');

            Route::get('kas-project', [App\Http\Controllers\RekapController::class, 'kas_project'])->name('rekap.kas-project');
            Route::post('/kas-project/void/{kasProject}', [App\Http\Controllers\RekapController::class, 'void_kas_project'])->name('rekap.kas-project.void');
            Route::get('/kas-project/print/{project}/{bulan}/{tahun}', [App\Http\Controllers\RekapController::class, 'kas_project_print'])->name('rekap.kas-project.print');

            Route::prefix('kas-investor')->group(function(){
                Route::get('/', [App\Http\Controllers\RekapController::class, 'rekap_investor'])->name('rekap.kas-investor');
                Route::get('/show/{investor}', [App\Http\Controllers\RekapController::class, 'rekap_investor_show'])->name('rekap.kas-investor.show');
                Route::get('/detail/{investor}', [App\Http\Controllers\RekapController::class, 'rekap_investor_detail'])->name('rekap.kas-investor.detail');
                Route::get('/detail-deviden/{investor}/show', [App\Http\Controllers\RekapController::class, 'rekap_investor_detail_deviden_show'])->name('rekap.kas-investor.detail-deviden.show');
                Route::get('/detail-deviden/{investor}', [App\Http\Controllers\RekapController::class, 'rekap_investor_detail_deviden'])->name('rekap.kas-investor.detail-deviden');
            });




        });
    });

    // END ROUTE REKAP
    Route::group(['middleware' => ['role:su,admin,user']], function() {
        Route::get('/billing', [App\Http\Controllers\BillingController::class, 'index'])->name('billing');
        Route::prefix('billing')->group(function() {

            Route::prefix('form-deposit')->group(function() {
                Route::get('/masuk', [App\Http\Controllers\FormDepositController::class, 'masuk'])->name('form-deposit.masuk');
                Route::post('/masuk/store', [App\Http\Controllers\FormDepositController::class, 'masuk_store'])->name('form-deposit.masuk.store');
                Route::get('/keluar', [App\Http\Controllers\FormDepositController::class, 'keluar'])->name('form-deposit.keluar');
                Route::post('/keluar/store', [App\Http\Controllers\FormDepositController::class, 'keluar_store'])->name('form-deposit.keluar.store');
                Route::get('/keluar-all', [App\Http\Controllers\FormDepositController::class, 'keluar_all'])->name('form-deposit.keluar-all');
                Route::post('/keluar-all/store', [App\Http\Controllers\FormDepositController::class, 'keluar_all_store'])->name('form-deposit.keluar-all.store');
            });


            Route::get('/form-kas-kecil/masuk', [App\Http\Controllers\FormKasKecilController::class, 'masuk'])->name('form-kas-kecil.masuk');
            Route::post('/form-kas-kecil/masuk/store', [App\Http\Controllers\FormKasKecilController::class, 'masuk_store'])->name('form-kas-kecil.masuk.store');
            Route::get('/form-kas-kecil/keluar', [App\Http\Controllers\FormKasKecilController::class, 'keluar'])->name('form-kas-kecil.keluar');
            Route::post('/form-kas-kecil/keluar/store', [App\Http\Controllers\FormKasKecilController::class, 'keluar_store'])->name('form-kas-kecil.keluar.store');

            Route::get('/form-lain/masuk', [App\Http\Controllers\FormLainController::class, 'masuk'])->name('form-lain.masuk');
            Route::post('/form-lain/masuk/store', [App\Http\Controllers\FormLainController::class, 'masuk_store'])->name('form-lain.masuk.store');
            Route::get('/form-lain/keluar', [App\Http\Controllers\FormLainController::class, 'keluar'])->name('form-lain.keluar');
            Route::post('/form-lain/keluar/store', [App\Http\Controllers\FormLainController::class, 'keluar_store'])->name('form-lain.keluar.store');

            Route::get('/form-transaksi', [App\Http\Controllers\FormTransaksiController::class, 'index'])->name('form-transaksi.index');
            Route::get('/form-transaksi/tambah/{customer}', [App\Http\Controllers\FormTransaksiController::class, 'tambah'])->name('form-transaksi.tambah');
            Route::post('/form-transaksi/tambah-store', [App\Http\Controllers\FormTransaksiController::class, 'tambah_store'])->name('form-transaksi.tambah-store');
            Route::get('/form-transaksi/masuk', [App\Http\Controllers\FormTransaksiController::class, 'masuk'])->name('form-transaksi.masuk');
            Route::post('/form-transaksi/masuk/store', [App\Http\Controllers\FormTransaksiController::class, 'masuk_store'])->name('form-transaksi.masuk.store');

            Route::get('/nota-tagihan', [App\Http\Controllers\NotaTagihanController::class, 'index'])->name('nota-tagihan.index');
            Route::post('/nota-tagihan/cicilan/{invoice}', [App\Http\Controllers\NotaTagihanController::class, 'cicilan'])->name('nota-tagihan.cicilan');
            Route::post('/nota-tagihan/cutoff/{invoice}', [App\Http\Controllers\NotaTagihanController::class, 'cutoff'])->name('nota-tagihan.cutoff');
            Route::post('/nota-tagihan/pelunasan/{invoice}', [App\Http\Controllers\NotaTagihanController::class, 'pelunasan'])->name('nota-tagihan.pelunasan');

            Route::prefix('nota-ppn-masukan')->group(function(){
                Route::get('/', [App\Http\Controllers\BillingController::class, 'nota_ppn_masukan'])->name('nota-ppn-masukan');
                Route::post('/claim/{kasProject}', [App\Http\Controllers\BillingController::class, 'claim_ppn'])->name('nota-ppn-masukan.claim');
            });

            Route::prefix('invoice-tagihan')->group(function () {
                Route::get('/', [App\Http\Controllers\BillingController::class, 'invoice_tagihan'])->name('invoice-tagihan');
            });

            Route::prefix('invoice-ppn')->group(function() {
                Route::get('/', [App\Http\Controllers\BillingController::class, 'invoice_ppn'])->name('invoice-ppn');
                Route::post('/bayar/{invoice}', [App\Http\Controllers\BillingController::class, 'invoice_ppn_bayar'])->name('invoice-ppn.bayar');
            });

            Route::prefix('ppn-susulan')->group(function() {
                Route::get('/', [App\Http\Controllers\BillingController::class, 'ppn_masuk_susulan'])->name('ppn-susulan');
                Route::post('/store', [App\Http\Controllers\BillingController::class, 'ppn_masuk_susulan_store'])->name('ppn-susulan.store');
            });

        });

    });

});
