<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\FormDepositController;
use App\Http\Controllers\FormKasKecilController;
use App\Http\Controllers\FormLabaSimpanController;
use App\Http\Controllers\FormLainController;
use App\Http\Controllers\FormTransaksiController;
use App\Http\Controllers\HistoriController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\InvestorModalController;
use App\Http\Controllers\LegalitasController;
use App\Http\Controllers\NotaTagihanController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\WaController;
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

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::group(['middleware' => ['role:su,admin']], function () {
        // ROUTE PENGATURAN
        // Route::view('pengaturan', 'pengaturan.index')->name('pengaturan');
        Route::prefix('pengaturan')->group(function () {
            Route::get('/', [PengaturanController::class, 'index_view'])->name('pengaturan');

            Route::get('/wa', [WaController::class, 'wa'])->name('pengaturan.wa');
            Route::get('/wa/get-wa-group', [WaController::class, 'get_group_wa'])->name('pengaturan.wa.get-group-wa');
            Route::get('/wa/edit/{id}', [WaController::class, 'edit'])->name('pengaturan.wa.edit');
            Route::patch('/wa/update/{id}', [WaController::class, 'update'])->name('pengaturan.wa.update');

            Route::get('/akun', [PengaturanController::class, 'index'])->name('pengaturan.akun');
            Route::post('/akun/store', [PengaturanController::class, 'store'])->name('pengaturan.akun.store');
            Route::patch('/akun/{akun}/update', [PengaturanController::class, 'update'])->name('pengaturan.akun.update');
            Route::delete('/akun/{akun}/delete', [PengaturanController::class, 'destroy'])->name('pengaturan.akun.delete');

            Route::post('/password-konfirmasi', [PengaturanController::class, 'password_konfirmasi'])->name('pengaturan.password-konfirmasi');
            Route::post('/password-konfirmasi/cek', [PengaturanController::class, 'password_konfirmasi_cek'])->name('pengaturan.password-konfirmasi-cek');

            Route::get('/histori-pesan', [HistoriController::class, 'index'])->name('pengaturan.histori-pesan');
            Route::post('/histori-pesan/resend/{pesanWa}', [HistoriController::class, 'resend'])->name('pengaturan.histori.resend');
            Route::delete('/histori-pesan/delete-sended', [HistoriController::class, 'delete_sended'])->name('pengaturan.histori.delete-sended');
        });
        // END ROUTE PENGATURAN

        Route::prefix('legalitas')->group(function () {

            Route::prefix('kategori')->group(function () {
                Route::post('/store', [LegalitasController::class, 'kategori_store'])->name('legalitas.kategori-store');
                Route::patch('/update/{id}', [LegalitasController::class, 'kategori_update'])->name('legalitas.kategori-update');
                Route::delete('/destroy/{id}', [LegalitasController::class, 'kategori_destroy'])->name('legalitas.kategori-destroy');
            });

            Route::get('/', [LegalitasController::class, 'index'])->name('legalitas');
            Route::post('/store', [LegalitasController::class, 'store'])->name('legalitas.store');
            Route::patch('/update/{legalitas}', [LegalitasController::class, 'update'])->name('legalitas.update');
            Route::delete('/destroy/{legalitas}', [LegalitasController::class, 'destroy'])->name('legalitas.destroy');

            Route::post('/kirim-wa/{legalitas}', [LegalitasController::class, 'kirim_wa'])->name('legalitas.kirim-wa');

        });

        Route::prefix('dokumen')->group(function () {
            Route::get('/', [DokumenController::class, 'index'])->name('dokumen');

            Route::prefix('mutasi-rekening')->group(function () {
                Route::get('/', [DokumenController::class, 'mutasi_rekening'])->name('dokumen.mutasi-rekening');
                Route::post('/store', [DokumenController::class, 'mutasi_rekening_store'])->name('dokumen.mutasi-rekening.store');
                Route::delete('/destroy/{mutasi}', [DokumenController::class, 'mutasi_rekening_destroy'])->name('dokumen.mutasi-rekening.destroy');
                Route::post('/kirim-wa/{mutasi}', [DokumenController::class, 'kirim_wa'])->name('dokumen.mutasi-rekening.kirim-wa');
            });

            Route::prefix('kontrak-tambang')->group(function () {
                Route::get('/', [DokumenController::class, 'kontrak_tambang'])->name('dokumen.kontrak-tambang');
                Route::post('/store', [DokumenController::class, 'kontrak_tambang_store'])->name('dokumen.kontrak-tambang.store');
                Route::delete('/destroy/{kontrak_tambang}', [DokumenController::class, 'kontrak_tambang_destroy'])->name('dokumen.kontrak-tambang.destroy');
                Route::post('/kirim-wa/{kontrak_tambang}', [DokumenController::class, 'kirim_wa_tambang'])->name('dokumen.kontrak-tambang.kirim-wa');
            });

            Route::prefix('kontrak-vendor')->group(function () {
                Route::get('/', [DokumenController::class, 'kontrak_vendor'])->name('dokumen.kontrak-vendor');
                Route::post('/store', [DokumenController::class, 'kontrak_vendor_store'])->name('dokumen.kontrak-vendor.store');
                Route::delete('/destroy/{kontrak_vendor}', [DokumenController::class, 'kontrak_vendor_destroy'])->name('dokumen.kontrak-vendor.destroy');
                Route::post('/kirim-wa/{kontrak_vendor}', [DokumenController::class, 'kirim_wa_vendor'])->name('dokumen.kontrak-vendor.kirim-wa');
            });

            Route::prefix('sph')->group(function () {
                Route::get('/', [DokumenController::class, 'sph'])->name('dokumen.sph');
                Route::post('/store', [DokumenController::class, 'sph_store'])->name('dokumen.sph.store');
                Route::delete('/destroy/{sph}', [DokumenController::class, 'sph_destroy'])->name('dokumen.sph.destroy');
                Route::post('/kirim-wa/{sph}', [DokumenController::class, 'kirim_wa_sph'])->name('dokumen.sph.kirim-wa');
            });
        });

        Route::prefix('company-profile')->group(function () {
            Route::get('/', [DokumenController::class, 'company_profile'])->name('company-profile');
            Route::post('/store', [DokumenController::class, 'company_profile_store'])->name('company-profile.store');
            Route::delete('/destroy/{company_profile}', [DokumenController::class, 'company_profile_destroy'])->name('company-profile.destroy');
            Route::post('/kirim-wa/{company_profile}', [DokumenController::class, 'kirim_wa_cp'])->name('company-profile.kirim-wa');
        });
    });

    // ROUTE DB
    Route::view('db', 'db.index')->name('db')->middleware('role:su,admin,user');
    Route::prefix('db')->group(function () {

        Route::group(['middleware' => ['role:su,admin,user']], function () {
            Route::get('/customer', [CustomerController::class, 'index'])->name('db.customer');
            Route::patch('/customer/{customer}/update-harga', [CustomerController::class, 'update_harga'])->name('db.customer.update-harga');
        });

        Route::group(['middleware' => ['role:su,admin']], function () {
            Route::post('/customer/store', [CustomerController::class, 'store'])->name('db.customer.store');
            Route::patch('/customer/{customer}/update', [CustomerController::class, 'update'])->name('db.customer.update');
            Route::delete('/customer/{customer}/delete', [CustomerController::class, 'destroy'])->name('db.customer.delete');

            Route::prefix('project')->group(function () {
                Route::get('/', [ProjectController::class, 'index'])->name('db.project');
                Route::post('/store', [ProjectController::class, 'store'])->name('db.project.store');
                Route::patch('/{project}/update', [ProjectController::class, 'update'])->name('db.project.update');
                Route::delete('/{project}/delete', [ProjectController::class, 'destroy'])->name('db.project.delete');
            });

            Route::get('/investor', [InvestorController::class, 'index'])->name('db.investor');
            Route::patch('/investor/{investor}/update', [InvestorController::class, 'update'])->name('db.investor.update');

            Route::get('/rekening', [RekeningController::class, 'index'])->name('db.rekening');
            Route::patch('/rekening/{rekening}/update', [RekeningController::class, 'update'])->name('db.rekening.update');

            Route::prefix('investor-modal')->group(function () {
                Route::get('/', [InvestorModalController::class, 'index'])->name('db.investor-modal');
                Route::post('/store', [InvestorModalController::class, 'store'])->name('db.investor-modal.store');
                Route::patch('/{investor}/update', [InvestorModalController::class, 'update'])->name('db.investor-modal.update');
                Route::delete('/{investor}/delete', [InvestorModalController::class, 'destroy'])->name('db.investor-modal.delete');
            });
        });
    });

    Route::group(['middleware' => ['role:su,admin,user,investor']], function () {
        Route::get('rekap', [RekapController::class, 'index'])->name('rekap');
        Route::prefix('rekap')->group(function () {
            Route::get('/kas-besar', [RekapController::class, 'kas_besar'])->name('rekap.kas-besar');
            Route::get('/kas-besar/print/{bulan}/{tahun}', [RekapController::class, 'kas_besar_print'])->name('rekap.kas-besar.print');
            Route::get('/kas-besar/detail-tagihan/{invoice}', [RekapController::class, 'detail_tagihan'])->name('rekap.kas-besar.detail-tagihan');
            Route::get('/kas-besar/detail-bayar/{invoice}', [RekapController::class, 'detail_bayar'])->name('rekap.kas-besar.detail-bayar');

            Route::get('/laba-simpan', [RekapController::class, 'laba_simpan'])->name('rekap.laba-simpan');

            Route::get('/kas-kecil', [RekapController::class, 'kas_kecil'])->name('rekap.kas-kecil');
            Route::get('/kas-kecil/print/{bulan}/{tahun}', [RekapController::class, 'kas_kecil_print'])->name('rekap.kas-kecil.print');
            Route::get('/kas-kecil/{kas}/void', [RekapController::class, 'void_kas_kecil'])->name('rekap.kas-kecil.void');

            Route::prefix('invoice')->group(function () {
                Route::get('/', [RekapController::class, 'rekap_invoice'])->name('rekap.invoice');
                Route::get('/detail-project', [RekapController::class, 'rekap_invoice_detail_project'])->name('rekap.invoice.detail-project');
            });

            Route::prefix('invoice-pph')->group(function () {
                Route::get('/', [RekapController::class, 'rekap_invoice_pph'])->name('rekap.invoice-pph');

                Route::get('/badan', [RekapController::class, 'pph_badan'])->name('rekap.pph-badan');
            });

            Route::get('/statistik/{customer}', [StatistikController::class, 'index'])->name('statistik.index');
            Route::get('/statistik/{customer}/print', [StatistikController::class, 'print'])->name('statistik.print');

            Route::get('kas-project', [RekapController::class, 'kas_project'])->name('rekap.kas-project');
            Route::post('/kas-project/void/{kasProject}', [RekapController::class, 'void_kas_project'])->name('rekap.kas-project.void');
            Route::get('/kas-project/print/{project}/{bulan}/{tahun}', [RekapController::class, 'kas_project_print'])->name('rekap.kas-project.print');

            Route::prefix('kas-investor')->group(function () {
                Route::get('/', [RekapController::class, 'rekap_investor'])->name('rekap.kas-investor');
                Route::get('/show/{investor}', [RekapController::class, 'rekap_investor_show'])->name('rekap.kas-investor.show');
                Route::get('/detail/{investor}', [RekapController::class, 'rekap_investor_detail'])->name('rekap.kas-investor.detail');
                Route::get('/detail-deviden/{investor}/show', [RekapController::class, 'rekap_investor_detail_deviden_show'])->name('rekap.kas-investor.detail-deviden.show');
                Route::get('/detail-deviden/{investor}', [RekapController::class, 'rekap_investor_detail_deviden'])->name('rekap.kas-investor.detail-deviden');
            });

        });
    });

    // END ROUTE REKAP
    Route::group(['middleware' => ['role:su,admin,user']], function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing');
        Route::prefix('billing')->group(function () {

            Route::prefix('form-deposit')->group(function () {
                Route::get('/masuk', [FormDepositController::class, 'masuk'])->name('form-deposit.masuk');
                Route::post('/masuk/store', [FormDepositController::class, 'masuk_store'])->name('form-deposit.masuk.store');
                Route::get('/keluar', [FormDepositController::class, 'keluar'])->name('form-deposit.keluar');
                Route::post('/keluar/store', [FormDepositController::class, 'keluar_store'])->name('form-deposit.keluar.store');
                Route::get('/keluar-all', [FormDepositController::class, 'keluar_all'])->name('form-deposit.keluar-all');
                Route::post('/keluar-all/store', [FormDepositController::class, 'keluar_all_store'])->name('form-deposit.keluar-all.store');
            });

            Route::prefix('form-kas-kecil')->group(function () {
                Route::get('/masuk', [FormKasKecilController::class, 'masuk'])->name('form-kas-kecil.masuk');
                Route::post('/masuk/store', [FormKasKecilController::class, 'masuk_store'])->name('form-kas-kecil.masuk.store');
                Route::get('/keluar', [FormKasKecilController::class, 'keluar'])->name('form-kas-kecil.keluar');
                Route::post('/keluar/store', [FormKasKecilController::class, 'keluar_store'])->name('form-kas-kecil.keluar.store');
            });

            Route::get('/form-lain/masuk', [FormLainController::class, 'masuk'])->name('form-lain.masuk');
            Route::post('/form-lain/masuk/store', [FormLainController::class, 'masuk_store'])->name('form-lain.masuk.store');
            Route::get('/form-lain/keluar', [FormLainController::class, 'keluar'])->name('form-lain.keluar');
            Route::post('/form-lain/keluar/store', [FormLainController::class, 'keluar_store'])->name('form-lain.keluar.store');

            Route::get('/form-laba-simpan/masuk', [FormLabaSimpanController::class, 'masuk'])->name('form-laba-simpan.masuk');
            Route::post('/form-laba-simpan/masuk/store', [FormLabaSimpanController::class, 'masuk_store'])->name('form-laba-simpan.masuk.store');
            Route::get('/form-laba-simpan/keluar', [FormLabaSimpanController::class, 'keluar'])->name('form-laba-simpan.keluar');
            Route::post('/form-laba-simpan/keluar/store', [FormLabaSimpanController::class, 'keluar_store'])->name('form-laba-simpan.keluar.store');

            Route::get('/form-transaksi', [FormTransaksiController::class, 'index'])->name('form-transaksi.index');
            Route::get('/form-transaksi/tambah/{customer}', [FormTransaksiController::class, 'tambah'])->name('form-transaksi.tambah');
            Route::post('/form-transaksi/tambah-store', [FormTransaksiController::class, 'tambah_store'])->name('form-transaksi.tambah-store');
            Route::get('/form-transaksi/masuk', [FormTransaksiController::class, 'masuk'])->name('form-transaksi.masuk');
            Route::post('/form-transaksi/masuk/store', [FormTransaksiController::class, 'masuk_store'])->name('form-transaksi.masuk.store');

            Route::get('/nota-tagihan', [NotaTagihanController::class, 'index'])->name('nota-tagihan.index');
            Route::post('/nota-tagihan/cicilan/{invoice}', [NotaTagihanController::class, 'cicilan'])->name('nota-tagihan.cicilan');
            Route::post('/nota-tagihan/cutoff/{invoice}', [NotaTagihanController::class, 'cutoff'])->name('nota-tagihan.cutoff');
            Route::post('/nota-tagihan/pelunasan/{invoice}', [NotaTagihanController::class, 'pelunasan'])->name('nota-tagihan.pelunasan');

            Route::prefix('nota-ppn-masukan')->group(function () {
                Route::get('/', [BillingController::class, 'nota_ppn_masukan'])->name('nota-ppn-masukan');
                Route::post('/claim/{kasProject}', [BillingController::class, 'claim_ppn'])->name('nota-ppn-masukan.claim');
            });

            Route::prefix('invoice-tagihan')->group(function () {
                Route::get('/', [BillingController::class, 'invoice_tagihan'])->name('invoice-tagihan');
            });

            Route::prefix('invoice-ppn')->group(function () {
                Route::get('/', [BillingController::class, 'invoice_ppn'])->name('invoice-ppn');
                Route::post('/bayar/{invoice}', [BillingController::class, 'invoice_ppn_bayar'])->name('invoice-ppn.bayar');
            });

            Route::prefix('ppn-susulan')->group(function () {
                Route::get('/', [BillingController::class, 'ppn_masuk_susulan'])->name('ppn-susulan');
                Route::post('/store', [BillingController::class, 'ppn_masuk_susulan_store'])->name('ppn-susulan.store');
            });

            Route::prefix('pph-disimpan')->group(function () {
                Route::get('/', [BillingController::class, 'pph_disimpan'])->name('pph-disimpan');
            });

        });

        Route::prefix('pajak')->group(function () {
            Route::get('/', [PajakController::class, 'index'])->name('pajak.index');
        });

    });

});
