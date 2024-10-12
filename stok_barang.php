<?php
session_start();
require 'config.php';
require 'login_session.php';

// Ambil data dari tabel barang
$barang = $conn->query("SELECT * FROM barang");
    
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

// Dapatkan nomor urut terbaru untuk idbarang baru
$stmt = $conn->query("SELECT idbarang FROM barang ORDER BY idbarang DESC LIMIT 1");
$latestidbarang= $stmt->fetch_assoc();
$urut = 1;
if ($latestidbarang) {
    $latestNumber = (int) substr($latestidbarang['idbarang'], 1);
    $urut = $latestNumber + 1;
}
$newidbarang = 'B' . str_pad($urut, 3, '0', STR_PAD_LEFT);

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
                    <h4>Barang</h4>
                    <div>
                    <button type="button" class="btn btn-primary mb-3 mr-2" data-bs-toggle="modal" data-bs-target="#addBarangModal">
                        <i class='fas fa-plus'></i> Add
                    </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="barangTable" style="border: 3px;" class="table table-striped table-bordered table-hover">    
                            <thead class="text-center table-info" >
                                <tr>
                                    <th style="width: 1%;">No</th>
                                    <th>Foto</th> 
                                    <th style="width: 1%;">Id Barang</th>
                                    <th>Nama</th>
                                    <th>Merk</th>
                                    <th>Deskripsi</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php
                                    if ($barang && $barang->num_rows > 0) {
                                    $no = 1;
                                    foreach($barang as $row): ?>
                                        <tr>
                                            <td> <?php echo $no++; ?> </td>
                                            <td> <img src="foto_barang/<?php echo htmlspecialchars($row['foto']);?>"
                                            alt="barang Photo" class="barang-photo"></td>
                                            <td> <?php echo $row["idbarang"]; ?> </td>
                                            <td> <?php echo $row["namabarang"]; ?> </td>
                                            <td> <?php echo $row["merk"]; ?> </td>
                                            <td> <?php echo $row["deskripsi"]; ?> </td>
                                            <td> <?php echo $row["stok"]; ?> </td>                                    
                                            <td> Rp <?php echo number_format($row["harga"], 0, ',', '.'); ?> </td>                               
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <button class='btn btn-warning btn-sm edit-btn mr-1' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editBarangModal'
                                                        data-idbarang='<?php echo htmlspecialchars($row['idbarang']); ?>'
                                                        data-namabarang='<?php echo htmlspecialchars($row['namabarang']); ?>'
                                                        data-merk='<?php echo htmlspecialchars($row['merk']); ?>'
                                                        data-deskripsi='<?php echo htmlspecialchars($row['deskripsi']); ?>'
                                                        data-stok='<?php echo htmlspecialchars($row['stok']); ?>'
                                                        data-harga='<?php echo htmlspecialchars($row['harga']); ?>' 
                                                        data-foto='<?php echo htmlspecialchars($row['foto']); ?>'>                                                        
                                                        <i class='fas fa-edit'></i> 
                                                    </button>
                                                    <button type="button" class='btn btn-success btn-sm print-btn' id="printButton"
                                                        data-id="<?php echo htmlspecialchars($row['idbarang']); ?>">
                                                        <i class='fas fa-print'>
                                                        </i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm delete-btn"
                                                                    data-id="<?php echo htmlspecialchars($row['idbarang']); ?>">
                                                                <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                        <?php } else { ?>
                                            <tr><td colspan="9" class="text-center">No data found</td></tr>
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

<!-- Modal Add Barang -->
<div class="modal fade" id="addBarangModal" tabindex="-1" aria-labelledby="addBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBarangModalLabel">Add Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_barang.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                    <label for="idbarang" class="form-label">Id barang</label>
                        <input type="text" class="form-control" id="add_idbarang" name="idbarang" value="<?php echo htmlspecialchars($newidbarang); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="add_namabarang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="add_namabarang" name="namabarang" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_merk" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="add_merk" name="merk" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_deskripsi" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" id="add_deskripsi" name="deskripsi"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="add_stok" name="stok"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="add_harga" name="harga"required>
                    </div>
                    <div class="mb-3">
                        <label for="foto_barang">Foto Barang</label> <br>
                        <input type="file" class="form-control" name="foto_barang" id="add_profile" accept=".jpg, .jpeg, .png, .jfif" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Barang -->
<div class="modal fade" id="editBarangModal" tabindex="-1" aria-labelledby="editBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBarangModalLabel">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_barang.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                    <label for="idbarang" class="form-label">Id barang</label>
                        <input type="text" class="form-control" id="edit_idbarang" name="idbarang" value="<?php echo htmlspecialchars($newidbarang); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_namabarang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="edit_namabarang" name="namabarang" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_merk" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="edit_merk" name="merk" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" id="edit_deskripsi" name="deskripsi"required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="edit_stok" name="stok"required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="edit_harga" name="harga"required>
                    </div>
                    <div class="mb-3">
                        <label for="foto_barang">Foto Barang</label> <br>
                        <img id="edit_foto" src="" alt="Current Barang Foto" class="mb-2" style="max-width: 100px;">
                        <input type="file" class="form-control" name="foto_barang" id="edit_profile" accept=".jpg, .jpeg, .png">
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

            $('#barangTable').DataTable().destroy();
            $('#barangTable').DataTable({
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
                const idbarang = this.getAttribute('data-idbarang');
                const namabarang = this.getAttribute('data-namabarang');
                const merk = this.getAttribute('data-merk');
                const deskripsi = this.getAttribute('data-deskripsi');
                const stok = this.getAttribute('data-stok');
                const harga = this.getAttribute('data-harga');
                const foto = this.getAttribute('data-foto');

                // Set values in the modal
                document.getElementById('edit_idbarang').value = idbarang;
                document.getElementById('edit_namabarang').value = namabarang;
                document.getElementById('edit_merk').value = merk;
                document.getElementById('edit_deskripsi').value = deskripsi;
                document.getElementById('edit_stok').value = stok;
                document.getElementById('edit_harga').value = harga;

                // document.getElementById('edit_foto').src = foto;

                 // Set the current profile picture source
                document.getElementById('edit_foto').src = 'foto_barang/' + foto; // Set the src for current profile picture
            });
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var idbarang = $(this).data('id');
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
                    url: 'delete_barang.php',
                    type: 'POST',
                    data: { idbarang: idbarang },
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
                window.open('print_barang.php?id=' + id, '_blank');
            });
        });
    });
</script>