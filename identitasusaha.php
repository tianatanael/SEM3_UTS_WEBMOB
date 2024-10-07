<?php
session_start();
require 'config.php';
require 'login_session.php';

// Ambil data dari tabel identitasusaha
$result = $conn->query("SELECT * FROM identitasusaha LIMIT 1");
$usaha = $result->fetch_assoc();

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


// Simpan pesan ke variabel dan hapus dari session
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
} 
?>

<!-- Bootstrap 5 source -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap and DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<?php require 'head.php'; ?>
<div class="wrapper">
    <header>
        <h4 style="text-align:center;"><?php echo htmlspecialchars($namaUsaha ?? ''); ?></h4>
        <p style="text-align:center;"><?php echo htmlspecialchars($alamatUsaha ?? ''); ?></p>
    </header>

    <?php include 'sidebar.php'; ?>
    <div class="content" id="content">
        <div class="container-fluid mt-3" style="margin-left:15px">
            <?php if (!$usaha) { 
                $newidusaha = "U001";
                ?>
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-primary mb-3 mr-2" data-bs-toggle="modal" data-bs-target="#addusahaModal"><i class='fas fa-plus'></i> Add Usaha </button>
            </div>
            <?php
            } else {
            ?>
            <div class="card col-md-12">
                <div class="card-title card-header">
                    <b>Identitas Usaha</b>
                </div>
                <div class="table table-bordered">
                    <table class="col-md-12">
                        <tr>
                            <th width="20%">Nama</th>
                            <td width="80%"> <?php echo $usaha["namausaha"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">Alamat</th>
                            <td width="80%"> <?php echo $usaha["alamat"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">No. Telepon</th>
                            <td width="80%"> <?php echo $usaha["notelepon"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">Fax</th>
                            <td width="80%"> <?php echo $usaha["fax"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">Email</th>
                            <td width="80%"> <?php echo $usaha["email"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">NPWP</th>
                            <td width="80%"> <?php echo $usaha["npwp"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">Bank</th>
                            <td width="80%"> <?php echo $usaha["bank"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">No. Account</th>
                            <td width="80%"> <?php echo $usaha["noaccount"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">Atas Nama</th>
                            <td width="80%"> <?php echo $usaha["atasnama"]; ?> </td>
                        </tr>
                        <tr>
                            <th width="20%">Pimpinan</th>
                            <td width="80%"> <?php echo $usaha["pimpinan"]; ?> </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-between align-items-center">
            <button class='btn btn-warning btn-sm edit-btn mr-1' 
                    data-bs-toggle='modal' 
                    data-bs-target='#editusaha'
                    data-idusaha='<?php echo htmlspecialchars($usaha['idusaha']); ?>'
                    data-nama='<?php echo htmlspecialchars($usaha['namausaha']); ?>'
                    data-alamat='<?php echo htmlspecialchars($usaha['alamat']); ?>'
                    data-notelepon='<?php echo htmlspecialchars($usaha['notelepon']); ?>'
                    data-fax='<?php echo htmlspecialchars($usaha['fax']); ?>'
                    data-email='<?php echo htmlspecialchars($usaha['email']); ?>'
                    data-npwp='<?php echo htmlspecialchars($usaha['npwp']); ?>'
                    data-bank='<?php echo htmlspecialchars($usaha['bank']); ?>'
                    data-noaccount='<?php echo htmlspecialchars($usaha['noaccount']); ?>'
                    data-atasnama='<?php echo htmlspecialchars($usaha['atasnama']); ?>'
                    data-pimpinan='<?php echo htmlspecialchars($usaha['pimpinan']); ?>'>
                <i class='fas fa-edit'></i> Edit
            </button>
            <button type="button" class='btn btn-success btn-sm print-btn' id="printButton" style="height: 30px;"
                data-id="<?php echo htmlspecialchars($row['idusaha']); ?>">
                <i class='fas fa-print'></i>
            </button>
            <button class="btn btn-danger btn-sm delete-btn"
                            data-id="<?php echo htmlspecialchars($usaha['idusaha']); ?>">
                        <i class="fas fa-trash"></i> Delete
            </button>
            </div>
            <br><br><br>
            <?php } ?>
        </div>
    </div>
    <?php require 'footer.php'; ?>
</div>

<!-- Modal Add Usaha -->
<div class="modal fade" id="addusahaModal" tabindex="-1" aria-labelledby="addusahaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addusahaModalLabel">Add Usaha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_usaha.php" method="post">
                    <div class="mb-3">
                    <label for="iddep" class="form-label">Id usaha</label>
                        <input type="text" class="form-control" id="idusaha" name="idusaha" value="<?php echo htmlspecialchars($newidusaha); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="add_nama" class="form-label">Nama Usaha</label>
                        <input type="text" class="form-control" id="add_nama" name="namausaha" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="add_alamat" name="alamat"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_notelepon" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="add_notelepon" minlength="10" maxlength="12" name="notelepon"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_fax" class="form-label">Fax</label>
                        <input type="text" class="form-control" id="add_fax" minlength="7" maxlength="8" name="fax"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="add_email" name="email"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_npwp" class="form-label">NPWP</label>
                        <input type="text" class="form-control" id="add_npwp" minlength="16" maxlength="16" name="npwp"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_bank" class="form-label">Bank</label>
                        <select class="form-control" id="add_bank" name="bank" required>
                            <option value="" disabled selected>Select a bank</option>
                            <option>BCA</option>
                            <option>BRI</option>
                            <option>BNI</option>
                            <option>Mandiri</option>
                            <option>BTN</option>
                            <option>MEGA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_noaccount" class="form-label">No Account</label>
                        <input type="text" class="form-control" id="add_noaccount" minlength="10" maxlength="15" name="noaccount"required>
                    </div>
                    <div class="mb-3">
                        <label for="add_atasnama" class="form-label">Atas Nama</label>
                        <input type="text" class="form-control" id="add_atasnama" name="atasnama" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_pimpinan" class="form-label">Pimpinan</label>
                        <input type="text" class="form-control" id="add_pimpinan" name="pimpinan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to prevent entering numbers in input
    document.querySelectorAll('input[id="add_pimpinan"] , input[id="add_atasnama"]').forEach(function(inputField) {
        inputField.addEventListener('input', function(e) {
            // Remove any non-letter characters (numbers, symbols, etc.) as the user types
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });

    document.querySelectorAll('input[id="add_notelepon"], input[id="add_fax"], input[id="add_npwp"], input[id="add_noaccount"]').forEach(function(inputField) {
        inputField.addEventListener('input', function(e) {
            // Replace any non-digit characters with an empty string
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>

<!-- Modal Edit Usaha -->
<div class="modal fade" id="editusaha" tabindex="-1" aria-labelledby="editusahaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editusahaModalLabel">Edit Usaha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_usaha.php" method="post">
                    <div class="mb-3">
                        <label for="edit_idusaha" class="form-label">Id Usaha</label>
                        <input type="text" class="form-control" id="edit_idusaha" name="idusaha" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Usaha</label>
                        <input type="text" class="form-control" id="edit_nama" name="namausaha" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="edit_alamat" name="alamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notelepon" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="edit_notelepon" name="notelepon" minlength="10" maxlength="12" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_fax" class="form-label">Fax</label>
                        <input type="text" class="form-control" id="edit_fax" name="fax" minlength="7" maxlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_npwp" class="form-label">NPWP</label>
                        <input type="text" class="form-control" id="edit_npwp" name="npwp" minlength="16" maxlength="16" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_bank" class="form-label">Bank</label>
                        <select class="form-control" id="edit_bank" name="bank" required>
                            <option value="" disabled selected>Select a bank</option>
                            <option>BCA</option>
                            <option>BRI</option>
                            <option>BNI</option>
                            <option>Mandiri</option>
                            <option>BTN</option>
                            <option>MEGA</option>
                        </select>
                        <!-- <input type="text" class="form-control" id="edit_bank" name="bank" required> -->
                    </div>
                    <div class="mb-3">
                        <label for="edit_noaccount" class="form-label">No Account</label>
                        <input type="text" class="form-control" id="edit_noaccount" name="noaccount" minlength="10" maxlength="15" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_atasnama" class="form-label">Atas Nama</label>
                        <input type="text" class="form-control" id="edit_atasnama" name="atasnama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_pimpinan" class="form-label">Pimpinan</label>
                        <input type="text" class="form-control" id="edit_pimpinan" name="pimpinan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to prevent entering numbers in input
    document.querySelectorAll('input[id="edit_pimpinan"] , input[id="edit_atasnama"]').forEach(function(inputField) {
        inputField.addEventListener('input', function(e) {
            // Remove any non-letter characters (numbers, symbols, etc.) as the user types
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
    });

    document.querySelectorAll('input[id="edit_notelepon"], input[id="edit_fax"], input[id="edit_npwp"], input[id="edit_noaccount"]').forEach(function(inputField) {
        inputField.addEventListener('input', function(e) {
            // Replace any non-digit characters with an empty string
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#checkDataButton').on('click', function() {
            // Perform an AJAX request to check if data exists in the 'namausaha' table
            $.ajax({
                url: 'cek_namausaha.php', // A new PHP file to handle the checking
                type: 'GET',
                success: function(response) {
                    // Parse the JSON response
                    var data = JSON.parse(response);
                    if (data.total > 0) {
                        // If data exists, show an alert or message
                        alert('Data usaha sudah ada. Anda tidak dapat menambahkan lagi.');
                    } else {
                        // If no data, open the modal
                        $('#addusahaModal').modal('show');
                    }
                }
            });
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
                const idusaha = this.getAttribute('data-idusaha');
                const nama = this.getAttribute('data-nama');
                const alamat = this.getAttribute('data-alamat');
                const notelepon = this.getAttribute('data-notelepon');
                const fax = this.getAttribute('data-fax');
                const email = this.getAttribute('data-email');
                const npwp = this.getAttribute('data-npwp');
                const bank = this.getAttribute('data-bank');
                const noaccount = this.getAttribute('data-noaccount');
                const atasnama = this.getAttribute('data-atasnama');
                const pimpinan = this.getAttribute('data-pimpinan');

                // Set values in the modal
                document.getElementById('edit_idusaha').value = idusaha;
                document.getElementById('edit_nama').value = nama;
                document.getElementById('edit_alamat').value = alamat;
                document.getElementById('edit_notelepon').value = notelepon;
                document.getElementById('edit_fax').value = fax;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_npwp').value = npwp;
                document.getElementById('edit_bank').value = bank;
                document.getElementById('edit_noaccount').value = noaccount;
                document.getElementById('edit_atasnama').value = atasnama;
                document.getElementById('edit_pimpinan').value = pimpinan;
            });
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var idusaha = $(this).data('id');
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
                    url: 'delete_usaha.php',
                    type: 'POST',
                    data: { idusaha: idusaha },
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

    // Print ke PDF        
    $(document).ready(function() {
        // Handle print button click
        $('#printButton').click(function() {
            window.open('print_usaha.php', '_blank');
        });
    });
</script>