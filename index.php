<?php
session_start();
include 'koneksi.php';

// Proses Tambah Data (CREATE)
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = (int)$_POST['nim'];
    $tgl_lahir = $_POST['tanggal_lahir'];
    $alamat_detail = mysqli_real_escape_string($koneksi, $_POST['alamat_detail']);
    $province_text = mysqli_real_escape_string($koneksi, $_POST['province_text']);
    $regency_text = mysqli_real_escape_string($koneksi, $_POST['regency_text']);
    $district_text = mysqli_real_escape_string($koneksi, $_POST['district_text']);
    $village_text = mysqli_real_escape_string($koneksi, $_POST['village_text']);
    $postal_code = (int)$_POST['postal_code'];

    // Check if nim already exists
    $query_check = "SELECT id FROM mahasiswa WHERE nim=$nim";
    $check = mysqli_query($koneksi, $query_check);
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('NIM sudah ada! Silakan gunakan NIM yang berbeda.'); window.history.back();</script>";
        exit();
    }

    // Gabungkan alamat lengkap
    $alamat_lengkap = $alamat_detail . ', Desa/Kel. ' . $village_text . ', Kec. ' . $district_text . ', Kab/Kota ' . $regency_text . ', Prov. ' . $province_text . ' [Kode Pos: ' . $postal_code . ']';

    $query = "INSERT INTO mahasiswa (nama, nim, tanggal_lahir, alamat_detail, province_text, regency_text, district_text, village_text, postal_code, alamat)
              VALUES ('$nama', $nim, '$tgl_lahir', '$alamat_detail', '$province_text', '$regency_text', '$district_text', '$village_text', $postal_code, '$alamat_lengkap')";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['pesan'] = 'sukses_tambah';
        header('Location: index.php');
        exit();
    } else {
        echo "<script>alert('Gagal menambah data! Error: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Proses Update Data (EDIT)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = (int)$_POST['nim'];
    $tgl_lahir = $_POST['tanggal_lahir'];
    $alamat_detail = mysqli_real_escape_string($koneksi, $_POST['alamat_detail']);
    $province_text = mysqli_real_escape_string($koneksi, $_POST['province_text']);
    $regency_text = mysqli_real_escape_string($koneksi, $_POST['regency_text']);
    $district_text = mysqli_real_escape_string($koneksi, $_POST['district_text']);
    $village_text = mysqli_real_escape_string($koneksi, $_POST['village_text']);
    $postal_code = (int)$_POST['postal_code'];

    // Check if nim already exists for other records
    $query_check = "SELECT id FROM mahasiswa WHERE nim=$nim AND id != $id";
    $check = mysqli_query($koneksi, $query_check);
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('NIM sudah ada! Silakan gunakan NIM yang berbeda.'); window.history.back();</script>";
        exit();
    }

    // Gabungkan alamat lengkap
    $alamat_lengkap = $alamat_detail . ', Desa/Kel. ' . $village_text . ', Kec. ' . $district_text . ', Kab/Kota ' . $regency_text . ', Prov. ' . $province_text . ' [Kode Pos: ' . $postal_code . ']';

    $query = "UPDATE mahasiswa SET nama='$nama', nim=$nim, tanggal_lahir='$tgl_lahir', alamat_detail='$alamat_detail', province_text='$province_text', regency_text='$regency_text', district_text='$district_text', village_text='$village_text', postal_code=$postal_code, alamat='$alamat_lengkap' WHERE id=$id";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['pesan'] = 'sukses_edit';
        header('Location: index.php');
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate data! Error: " . mysqli_error($koneksi) . "');</script>";
    }
}

// Proses Delete Data
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM mahasiswa WHERE id='$id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['pesan'] = 'sukses_hapus';
        header('Location: index.php');
        exit();
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Akademik - Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>

    </style>
</head>
<body>
<div class="container">
    <div class="header-section text-center">
        <h1 class="display-4"><i class="fas fa-graduation-cap me-2"></i> Sistem Informasi Akademik</h1>
        <p class="lead">Manajemen Data Mahasiswa (CRUD)</p>
    </div>

    <?php
    if (isset($_SESSION['pesan'])) {
        $pesan = $_SESSION['pesan'];
        $alert_class = ''; $icon = ''; $message = '';
        if ($pesan == 'sukses_tambah') {
            $alert_class = 'alert-success'; $icon = 'fas fa-check-circle'; $message = 'Data Mahasiswa berhasil ditambahkan!';
        } elseif ($pesan == 'sukses_edit') {
            $alert_class = 'alert-warning'; $icon = 'fas fa-edit'; $message = 'Data Mahasiswa berhasil diperbarui!';
        } elseif ($pesan == 'sukses_hapus') {
            $alert_class = 'alert-danger'; $icon = 'fas fa-trash-alt'; $message = 'Data Mahasiswa berhasil dihapus!';
        }
        if ($alert_class) {
            echo "<div class='alert {$alert_class} alert-dismissible fade show' role='alert'><i class='{$icon} me-2'></i> {$message}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }
        unset($_SESSION['pesan']);
    }
    ?>

    <button type="button" class="btn btn-primary btn-lg mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="fas fa-plus-circle me-1"></i> Tambah Data
    </button>

    <div class="table-responsive">
        <table id="dataMahasiswa" class="table table-custom table-striped align-middle">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-id-card"></i> NIM</th>
                    <th><i class="fas fa-user"></i> Nama</th>
                    <th><i class="fas fa-calendar-alt"></i> Tanggal Lahir</th>
                    <th><i class="fas fa-map-marker-alt"></i> Alamat Lengkap</th>
                    <th class="text-center"><i class="fas fa-cogs"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query TAMPIL DIBUAT SEDERHANA, karena kolom ID wilayah di tabel 'mahasiswa' tidak terisi lagi.
                $query_tampil = "SELECT * FROM mahasiswa ORDER BY nim ASC";
                $data_mahasiswa = mysqli_query($koneksi, $query_tampil);

                $no = 1;
                if (mysqli_num_rows($data_mahasiswa) > 0) {
                    while ($d = mysqli_fetch_array($data_mahasiswa)) {
                        $alamat_tampil = $d['alamat'];
                ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $d['nim']; ?></td>
                            <td><?php echo $d['nama']; ?></td>
                            <td><?php echo date('d M Y', strtotime($d['tanggal_lahir'])); ?></td>
                            <td class="small"><?php echo $alamat_tampil; ?></td> 
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $d['id']; ?>" data-nim="<?php echo $d['nim']; ?>" data-nama="<?php echo $d['nama']; ?>" data-tanggal_lahir="<?php echo $d['tanggal_lahir']; ?>" data-province_text="<?php echo $d['province_text']; ?>" data-regency_text="<?php echo $d['regency_text']; ?>" data-district_text="<?php echo $d['district_text']; ?>" data-village_text="<?php echo $d['village_text']; ?>" data-alamat_detail="<?php echo $d['alamat_detail']; ?>" data-postal_code="<?php echo $d['postal_code']; ?>" title="Edit Data"><i class="fas fa-pen"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $d['id']; ?>" data-nama="<?php echo $d['nama']; ?>" title="Hapus Data"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center text-muted">Belum ada data mahasiswa.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> 
    <div class="modal-content">
        <div class="modal-header modal-header-primary">
            <h5 class="modal-title" id="tambahModalLabel"><i class="fas fa-user-plus me-2"></i> Tambah Data Mahasiswa Baru</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="index.php">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nim" name="nim" required placeholder="NIM">
                            <label for="nim">NIM</label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nama" name="nama" required placeholder="Nama Lengkap">
                            <label for="nama">Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-2 text-primary"><i class="fas fa-map-marker-alt me-1"></i> Detail Alamat</h6>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="province_text" name="province_text" required placeholder="Provinsi">
                            <label for="province_text">Provinsi</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="regency_text" name="regency_text" required placeholder="Kabupaten/Kota">
                            <label for="regency_text">Kabupaten/Kota</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="district_text" name="district_text" required placeholder="Kecamatan">
                            <label for="district_text">Kecamatan</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="village_text" name="village_text" required placeholder="Kelurahan/Desa">
                            <label for="village_text">Kelurahan/Desa</label>
                        </div>
                    </div>
                    </div>
                
                <div class="row g-2">
                    <div class="col-md-10 row g-3 mt-2">
                        <div class="form-floating">
                            <textarea class="form-control" id="alamat_detail" name="alamat_detail" rows="3" required placeholder="Jalan, Nomor Rumah, RT/RW, Dll." style="height: 50px;"></textarea>
                            <label for="alamat_detail">   Alamat Lengkap (Jalan, Nomor Rumah, RT/RW, Dll.)</label>
                        </div>
                    </div>
                    
                    <div class="col-md-2 row g-3 mt-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="postal_code" name="postal_code" required placeholder="Kode Pos">
                            <label for="postal_code">   Kode Pos</label>
                        </div>
                    </div>
                </div>   

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" name="tambah" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Data</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header modal-header-primary">
            <h5 class="modal-title" id="editModalLabel"><i class="fas fa-user-edit me-2"></i> Edit Data Mahasiswa</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="index.php">
            <div class="modal-body">
                <input type="hidden" id="edit_id" name="id">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_nim" name="nim" required placeholder="NIM">
                            <label for="edit_nim">NIM</label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_nama" name="nama" required placeholder="Nama Lengkap">
                            <label for="edit_nama">Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir" required>
                            <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-2 text-primary"><i class="fas fa-map-marker-alt me-1"></i> Detail Alamat</h6>

                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_province_text" name="province_text" required placeholder="Provinsi">
                            <label for="edit_province_text">Provinsi</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_regency_text" name="regency_text" required placeholder="Kabupaten/Kota">
                            <label for="edit_regency_text">Kabupaten/Kota</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_district_text" name="district_text" required placeholder="Kecamatan">
                            <label for="edit_district_text">Kecamatan</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_village_text" name="village_text" required placeholder="Kelurahan/Desa">
                            <label for="edit_village_text">Kelurahan/Desa</label>
                        </div>
                    </div>
                    </div>

                <div class="row g-2">
                    <div class="col-md-10 row g-3 mt-2">
                        <div class="form-floating">
                            <textarea class="form-control" id="edit_alamat_detail" name="alamat_detail" rows="3" required placeholder="Jalan, Nomor Rumah, RT/RW, Dll." style="height: 50px;"></textarea>
                            <label for="edit_alamat_detail">   Alamat Lengkap (Jalan, Nomor Rumah, RT/RW, Dll.)</label>
                        </div>
                    </div>

                    <div class="col-md-2 row g-3 mt-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required placeholder="Kode Pos">
                            <label for="edit_postal_code">   Kode Pos</label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" name="update" class="btn btn-warning"><i class="fas fa-sync-alt me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-trash-alt me-2"></i> Konfirmasi Hapus</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="index.php">
            <div class="modal-body">
                <input type="hidden" id="delete_id" name="id">
                <p>Apakah Anda yakin ingin menghapus data <strong id="delete_nama"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="delete" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Hapus</button>
            </div>
        </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Inisialisasi DataTables tanpa pencarian, paginasi, dan info
    var table = $('#dataMahasiswa').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json" // Bahasa Indonesia
        },
        "columnDefs": [
            { "orderable": false, "targets": 5 } // Kolom Aksi tidak bisa diurutkan
        ],
        "order": [[1, 'asc']], // Urutkan berdasarkan kolom NIM secara default
        "searching": false, // Hilangkan search box
        "lengthChange": false, // Hilangkan dropdown "Show entries"
        "paging": false, // Hilangkan paginasi
        "info": false // Hilangkan info "Showing x to y of z entries"
    });

    // Animasi pada isi tabel saat sorting
    table.on('order.dt', function() {
        $('.table-custom tbody tr').hide().fadeIn(300);
    });

    // Fill edit modal fields
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nim = button.data('nim');
        var nama = button.data('nama');
        var tanggal_lahir = button.data('tanggal_lahir');
        var province_text = button.data('province_text');
        var regency_text = button.data('regency_text');
        var district_text = button.data('district_text');
        var village_text = button.data('village_text');
        var alamat_detail = button.data('alamat_detail');
        var postal_code = button.data('postal_code');

        $('#edit_id').val(id);
        $('#edit_nim').val(nim);
        $('#edit_nama').val(nama);
        $('#edit_tanggal_lahir').val(tanggal_lahir);
        $('#edit_province_text').val(province_text);
        $('#edit_regency_text').val(regency_text);
        $('#edit_district_text').val(district_text);
        $('#edit_village_text').val(village_text);
        $('#edit_alamat_detail').val(alamat_detail);
        $('#edit_postal_code').val(postal_code);
    });

    // Fill delete modal fields
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');

        $('#delete_id').val(id);
        $('#delete_nama').text(nama);
    });
});
</script>
</body>
</html>