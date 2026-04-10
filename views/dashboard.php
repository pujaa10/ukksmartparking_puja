<?php
// DATA DARI CONTROLLER
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Parkir RFID</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- 🔥 AUTO REFRESH -->
<meta http-equiv="refresh" content="3">

<style>
:root { 
  --primary: #ec4899; 
  --primary-dark: #3b82f6; 
  --sidebar-light: #f9a8d4; 
  --sidebar-dark: #93c5fd; 
  --success: #16a34a; 
  --danger: #dc2626; 
  --info: #2563eb;
  --text: #1e293b; 
  --border-soft: #cbd5e1;
} 

* {
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
    font-family: "Inter", "Segoe UI", Arial, sans-serif;
} 

body { 
    min-height: 100vh; 
    background: linear-gradient(135deg, #fce7f3, #dbeafe); 
    color: var(--text);
} 

.sidebar {
    width: 100%;
    height: 70px; 
    position: fixed; 
    top: 0;
    left: 0; 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    padding: 0 30px; 
    background: linear-gradient(90deg, var(--sidebar-light), var(--sidebar-dark)); 
    box-shadow: 0 3px 14px rgba(0,0,0,0.08);
} 

.sidebar h3 { 
    color: white;
    font-size: 20px;
}

.sidebar a { 
    text-decoration: none; 
    font-weight: 600; 
    color: white; 
    padding: 8px 14px; 
    border-radius: 10px; 
    background: linear-gradient(90deg, #fb7185, #ec4899);
}

.content { 
    margin-top: 100px; 
    padding: 32px;
} 

h2 { 
    margin-bottom: 20px;
}

table { 
    width: 100%; 
    border-collapse: collapse; 
    background: #ffffff; 
    border-radius: 16px; 
    overflow: hidden; 
    border: 1px solid var(--border-soft); 
    box-shadow: 0 6px 16px rgba(0,0,0,0.06); 
    margin-bottom: 40px;
} 

th { 
    background: linear-gradient(90deg, #f9a8d4, #93c5fd); 
    padding: 14px; 
    font-size: 14px; 
    color: white;
} 

td { 
    padding: 13px;
    text-align: center; 
    border-bottom: 1px solid var(--border-soft);
} 

tr:hover td { 
    background: #f1f5f9;
} 

.in { color: var(--success); font-weight: 700; }
.out { color: var(--danger); font-weight: 700; }
.done { color: var(--info); font-weight: 700; }

.btn { 
    padding: 7px 14px;
    font-size: 13px;
    border-radius: 10px; 
    background: linear-gradient(135deg, #f9a8d4, #93c5fd); 
    color: white; 
    text-decoration: none;
}
</style>
</head>

<body>

<div class="sidebar">
  <h3><i class="fa-solid fa-car"></i> Go-Park</h3>
  <a href="../logout.php">Logout</a>
</div>

<div class="content">

<!-- ===================== -->
<!-- CHECK-IN -->
<!-- ===================== -->
<h2>Data Check-in</h2>
<table>
<tr>
<th>ID</th>
<th>RFID</th>
<th>Waktu Masuk</th>
<th>Status</th>
</tr>

<?php while($d = mysqli_fetch_assoc($dataMasuk)) { ?>
<tr>
<td><?= htmlspecialchars($d['id']) ?></td>
<td><?= htmlspecialchars($d['rfid']) ?></td>
<td><?= $d['waktu_masuk'] ?></td>
<td class="in">IN</td>
</tr>
<?php } ?>
</table>

<!-- ===================== -->
<!-- CHECK-OUT -->
<!-- ===================== -->
<h2>Data Check-out</h2>
<table>
<tr>
<th>ID</th>
<th>RFID</th>
<th>Waktu Masuk</th>
<th>Waktu Keluar</th>
<th>Durasi</th>
<th>Total Biaya</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<?php while($d = mysqli_fetch_assoc($dataKeluar)) { ?>
<tr style="background:#fee2e2;">
<td><?= htmlspecialchars($d['id']) ?></td>
<td><?= htmlspecialchars($d['rfid']) ?></td>
<td><?= $d['waktu_masuk'] ?></td>
<td><?= $d['waktu_keluar'] ?></td>

<!-- 🔥 PAKAI duration DARI DB -->
<td><?= ceil($d['duration'] / 3600) ?> jam</td>

<!-- 🔥 PAKAI fee DARI DB -->
<td>Rp <?= number_format($d['biaya']) ?></td>

<td class="out">OUT</td>

<td>
<a class="btn"
href="../controllers/ParkirController.php?history_id=<?= $d['id'] ?>"
onclick="return confirm('Buka palang & selesaikan transaksi?')">
Buka Palang
</a>
</td>

</tr>
<?php } ?>
</table>

<!-- ===================== -->
<!-- HISTORY -->
<!-- ===================== -->
<h2>History Parkir</h2>
<table>
<tr>
<th>ID</th>
<th>RFID</th>
<th>Masuk</th>
<th>Keluar</th>
<th>Durasi</th>
<th>Biaya</th>
<th>Status</th>
<th>Cetak</th>
</tr>

<?php while($d = mysqli_fetch_assoc($dataHistory)) { ?>
<tr>
<td><?= $d['id'] ?></td>
<td><?= $d['rfid'] ?></td>
<td><?= $d['waktu_masuk'] ?></td>
<td><?= $d['waktu_keluar'] ?></td>

<td><?= ceil($d['duration'] / 3600) ?> jam</td>
<td>Rp <?= number_format($d['biaya']) ?></td>

<td class="done">DONE</td>

<td>
<a class="btn" href="#"
onclick="showStruk(
'<?= $d['id'] ?>',
'<?= $d['rfid'] ?>',
'<?= $d['waktu_masuk'] ?>',
'<?= $d['waktu_keluar'] ?>',
'<?= $d['duration'] ?>',
'<?= $d['biaya'] ?>'
)">
<i class="fa fa-print"></i>
</a>
</td>
</tr>
<?php } ?>
</table>

</div>

<!-- ===================== -->
<!-- STRUK -->
<!-- ===================== -->
<div id="strukModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;">

<div id="strukContent" style="background:white;padding:20px;border-radius:10px;width:300px;text-align:center;font-family:monospace;">

<h3>GO-PARK</h3><hr>

<p id="s_id"></p>
<p id="s_rfid"></p><hr>

<p id="s_masuk"></p>
<p id="s_keluar"></p><hr>

<p id="s_durasi"></p>
<p id="s_biaya"></p><hr>

<p>Terima Kasih</p><br>

<button onclick="printStruk()">Print</button>
<button onclick="closeStruk()">Tutup</button>

</div>
</div>

<script>
function showStruk(id, rfid, masuk, keluar, duration, biaya) {

    let jam = Math.ceil(duration / 3600);

    document.getElementById("s_id").innerText = "ID: " + id;
    document.getElementById("s_rfid").innerText = "RFID: " + rfid;
    document.getElementById("s_masuk").innerText = "Masuk: " + masuk;
    document.getElementById("s_keluar").innerText = "Keluar: " + keluar;
    document.getElementById("s_durasi").innerText = "Durasi: " + jam + " jam";
    document.getElementById("s_biaya").innerText = "Total: Rp " + biaya;

    document.getElementById("strukModal").style.display = "flex";
}

function closeStruk() {
    document.getElementById("strukModal").style.display = "none";
}

function printStruk() {
    let content = document.getElementById("strukContent").innerHTML;
    let win = window.open('', '', 'width=300,height=500');
    win.document.write(content);
    win.print();
    win.close();
}
</script>

</body>
</html>