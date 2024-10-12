<?php
session_start();
require 'config.php';
require 'login_session.php';

// Ambil data dari tabel barang
$barang = $conn->query("SELECT * FROM barang");
$penjualan = $conn->query("SELECT p.idjual, p.idbarang, p.tanggal, p.jumlah, p.hargasatuan, b.namabarang, b.merk FROM penjualan p JOIN barang b ON p.idbarang = b.idbarang ORDER BY p.tanggal");
    
$staffid = $_SESSION['staffid'];

// Ambil data user dari database
$stmt = $conn->prepare("SELECT username, foto FROM login WHERE staffid = ?");
$stmt->bind_param("i", $staffid);
$stmt->execute();
$stmt->bind_result($username, $foto);
$stmt->fetch();
$stmt->close();

// Ambil data nama usaha dan alamat dari database
$stmt = $conn->prepare("SELECT namausaha, alamat FROM identitasusaha LIMIT 1");
$stmt->execute();
$stmt->bind_result($namaUsaha, $alamatUsaha);
$stmt->fetch();
$stmt->close();

// Dapatkan nomor urut terbaru untuk idjual baru
$stmt = $conn->query("SELECT idjual FROM penjualan ORDER BY idjual DESC LIMIT 1");
$latestidjual= $stmt->fetch_assoc();
$urut = 1;
if ($latestidjual) {
    $latestNumber = (int) substr($latestidjual['idjual'], 2);
    $urut = $latestNumber + 1;
}
$newidjual = 'PB' . str_pad($urut, 7, '0', STR_PAD_LEFT);

// Simpan pesan ke variabel dan hapus dari session
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<link rel="stylesheet" href="/JAYA_ELEKTRONIK/CSS/barang.css">
<!-- Bootstrap 5 source -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require 'head.php'; ?>
<div class="wrapper">
    <header>
        <h4 style="text-align:center;"><?php echo htmlspecialchars($namaUsaha ?? ''); ?></h4>
        <p style="text-align:center;"><?php echo htmlspecialchars($alamatUsaha ?? ''); ?></p>
    </header>
    <?php include 'sidebar.php'; ?>
    <div class="content" id="content">
        <div class="container-fluid mt-3" style="margin-left:15px">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <h4>Penjualan</h4>
                    <div>
                    <button type="button" class="btn btn-primary mb-3 mr-2" data-bs-toggle="modal" data-bs-target="#addPenjualanModal">
                        <i class='fas fa-plus'></i> Add
                    </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="penjualanTable" style="border: 3px;" class="table table-striped table-bordered table-hover">    
                            <thead class="text-center table-info" >
                                <tr>
                                    <th style="width: 1%;">No</th>
                                    <th>ID Penjualan</th> 
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Merk</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Harga Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php
                                    if ($penjualan && $penjualan->num_rows > 0) {
                                    $no = 1;
                                    foreach($penjualan as $row): ?>
                                        <tr>
                                            <td> <?php echo $no++; ?> </td>
                                            <td> <?php echo $row['idjual'];?></td>
                                            <td> <?php echo $row["idbarang"]; ?> </td>
                                            <td> <?php echo $row["namabarang"]; ?> </td>
                                            <td> <?php echo $row["merk"]; ?> </td>
                                            <td> <?php echo $row["tanggal"]; ?> </td>
                                            <td> <?php echo $row["jumlah"]; ?> </td>                                    
                                            <td> Rp <?php echo number_format($row["hargasatuan"], 0, ',', '.'); ?> </td>                               
                                            <td> Rp <?php echo number_format($row["hargasatuan"] * $row['jumlah'], 0, ',', '.'); ?> </td>                               
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <button class='btn btn-warning btn-sm edit-btn mr-1' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editPenjualanModal'
                                                        data-id='<?php echo htmlspecialchars($row['idjual']) ?>'
                                                        data-idbarang='<?php echo htmlspecialchars($row['idbarang']); ?>'
                                                        data-namabarang='<?php echo htmlspecialchars($row['namabarang']); ?>'
                                                        data-jumlah='<?php echo htmlspecialchars($row['jumlah']); ?>'
                                                        data-tanggal='<?php echo htmlspecialchars($row['tanggal']); ?>'
                                                        data-hargasatuan='<?php echo htmlspecialchars($row['hargasatuan']); ?>'>                                                        
                                                        <i class='fas fa-edit'></i> 
                                                    </button>
                                                    <button type="button" class='btn btn-success btn-sm print-btn' id="printButton"
                                                        data-id="<?php echo htmlspecialchars($row['idjual']); ?>">
                                                        <i class='fas fa-print'>
                                                        </i>
                                                    </button>
                                                    &nbsp;
                                                    <button class="btn btn-danger btn-sm delete-btn"
                                                                    data-id="<?php echo htmlspecialchars($row['idjual']); ?>">
                                                                <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                        <?php } else { ?>
                                            <tr><td colspan="10" class="text-center">No data found</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require 'footer.php'; ?>
</div>

<!-- Modal Add Penjualan -->
<div class="modal fade" id="addPenjualanModal" tabindex="-1" aria-labelledby="addPenjualanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPenjualanModalLabel">Add Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_penjualan.php" method="post">
                    <div class="mb-3">
                    <label for="idjual" class="form-label">ID Penjualan</label>
                        <input type="text" class="form-control" id="add_idjual" name="idjual" value="<?php echo htmlspecialchars($newidjual); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="add_idbarang" class="form-label">Nama Barang</label>
                        <select class="form-select" name="idbarang" id="add_idbarang">
                            <option value="" selected disabled>Pilih Barang</option>
                            <?php
                            $barang = $conn->query("SELECT idbarang, namabarang FROM barang ORDER BY namabarang");
                            while ($row = $barang->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['idbarang']) . "'>"
                                . htmlspecialchars($row['namabarang']) . "</option>";
                            }
                            ?>
                        </select>
                        
                    </div>                    
                    <div class="mb-3">
                        <label for="add_tanggal" class="form-label">Tanggal Penjualan</label>
                        <input type="date" class="form-control" id="add_tanggal" name="tanggal"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="add_jumlah" name="jumlah" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Penjualan -->
<div class="modal fade" id="editPenjualanModal" tabindex="-1" aria-labelledby="editPenjualanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPenjualanModalLabel">Edit Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_penjualan.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                    <label for="idjual" class="form-label">ID Penjualan</label>
                        <input type="text" class="form-control" id="edit_idjual" name="idjual" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_idbarang" class="form-label">ID Barang</label>
                        <select class="form-control" id="edit_idbarang" name="idbarang" required>
                            
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tanggal" class="form-label">Tanggal Penjualan</label>
                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal"required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_hargasatuan" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="edit_hargasatuan" name="hargasatuan" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap 5 source -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap and DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    //Pagination
    $(document).ready(function() {
        // Adjust DataTables' scrolling to avoid overlapping with the footer
        function adjustTableHeight() {
            var footerHeight = $('footer').outerHeight();
            var tableHeight = 'calc(100vh - 290px - ' + footerHeight + 'px)';

            $('#penjualanTable').DataTable().destroy();
            $('#penjualanTable').DataTable({
                "pagingType": "simple_numbers",
                "scrollY": tableHeight,
                "scrollCollapse": true,
                "paging": true
            });
        }

        // Call the function to adjust table height initially
        adjustTableHeight();

        // Adjust table height on window resize
        $(window).resize(function() {
            adjustTableHeight();
        });
    });

    // Show message if it exists in the session
    <?php if ($message): ?>
        Swal.fire({
            title: '<?php echo $message['type'] === 'success' ? 'Success!' : 'Error!'; ?>',
            text: '<?php echo $message['text']; ?>',
            icon: '<?php echo $message['type'] === 'success' ? 'success' : 'error'; ?>'
        });
    <?php endif; ?>     

    document.addEventListener('DOMContentLoaded', function () {
        // Add event listener to all edit buttons
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Get data attributes from the button
                const idjual = this.getAttribute('data-id');
                const jumlah = this.getAttribute('data-jumlah');
                const tanggal = this.getAttribute('data-tanggal');
                const hargasatuan = this.getAttribute('data-hargasatuan');

                const idbarang = this.getAttribute('data-idbarang');
                const namabarang = this.getAttribute('data-namabarang');

                // Set values in the modal
                document.getElementById('edit_idjual').value = idjual;
                document.getElementById('edit_jumlah').value = jumlah;
                document.getElementById('edit_tanggal').value = tanggal;
                document.getElementById('edit_hargasatuan').value = hargasatuan;

                // Set combobox nama pegawai
                const editBarangSelect = document.getElementById('edit_idbarang');
                editBarangSelect.innerHTML = `<option value="${idbarang}">${namabarang}</option>`;

                // Load seluruh pegawai dari database saat combobox diklik
                editBarangSelect.addEventListener('click', function() {
                    if (editBarangSelect.options.length === 1) { // Jika belum pernah load data pegawai
                        $.ajax({
                            url: 'get_barang_list.php',
                            method: 'GET',
                            success: function(response) {
                                editBarangSelect.innerHTML = response;
                                editBarangSelect.value = idbarang; // Pastikan pegawai yang sedang dipilih tetap terpilih
                            },
                            error: function(xhr, status, error) {
                                console.error('Gagal mendapatkan daftar barang:', error);
                            }
                        });
                    };
                });
            })
        });
    });
    

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var idjual = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'Apa benar data tersebut dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_penjualan.php',
                    type: 'POST',
                    data: { idjual: idjual },
                    success: function(response) {
                        console.log(response); // Debugging
                        if (response.includes('Success')) {
                            Swal.fire(
                                'Deleted!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText); // Debugging
                        Swal.fire(
                            'Error!',
                            'An error occurred: ' + error,
                            'error'
                        );
                    }
                });
            }
        });
    });

    //Print ke PDF 
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.print-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                window.open('print_penjualan.php?id=' + id, '_blank');
            });
        });
    });
</script>