<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST['nama_lengkap'];
    $jabatan = $_POST['jabatan'];
    $pemberi_bantuan = $_POST['pemberi_bantuan'];
    $judul_bantuan = $_POST['judul_bantuan'];
    $npsn = $_POST['npsn'];
    $jenis_lembaga = $_POST['jenis_lembaga'];
    $alamat_lembaga = $_POST['alamat_lembaga'];
    $npwp = $_POST['npwp'];
    $nama_bank = $_POST['nama_bank'];
    $nama_rekening = $_POST['nama_rekening'];
    $nomor_rekening = $_POST['nomor_rekening'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan Pencairan Dana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 1px solid #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .signature {
            margin-top: 50px;
        }
        .print-button {
            margin-top: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center;">SURAT PERMOHONAN PENCAIRAN DANA</h2>
        <p>Dengan hormat yang bertanda tangan di bawah ini:</p>
        <p>Nama Lengkap: <?php echo $nama_lengkap; ?><br>
        Jabatan: <?php echo $jabatan; ?></p>

        <p>Bersama ini mengajukan permohonan pencairan dana bantuan:</p>
        <p>Pemberi Bantuan: <?php echo $pemberi_bantuan; ?><br>
        Judul Bantuan: <?php echo $judul_bantuan; ?></p>

        <p>Dengan data penerima sebagai berikut:</p>
        <p>Nomor Statistik: <?php echo $npsn; ?><br>
        Jenis Lembaga: <?php echo $jenis_lembaga; ?><br>
        Alamat Lembaga: <?php echo nl2br($alamat_lembaga); ?><br>
        NPWP: <?php echo $npwp; ?></p>

        <table>
            <tr>
                <th>Nama Bank</th>
                <th>Nama Rekening</th>
                <th>Nomor Rekening</th>
            </tr>
            <tr>
                <td><?php echo $nama_bank; ?></td>
                <td><?php echo $nama_rekening; ?></td>
                <td><?php echo $nomor_rekening; ?></td>
            </tr>
        </table>

        <p>Bersama permohonan ini terlampir kelengkapan administrasi pencairan bantuan yang telah dilengkapi dan ditandatangani oleh penerima bantuan meliputi:</p>
        <ol>
            <li>Perjanjian antara PPK dan penerima bantuan;</li>
            <li>Kuitansi bukti penerimaan Uang; dan</li>
            <li>Surat pernyataan penerima bantuan pemerintah.</li>
        </ol>

        <div class="signature">
            <p>---------------------<br>
            ---------------------<br>
            ---------------------</p>
        </div>

        <div class="print-button">
            <button onclick="window.print()">Print</button>
        </div>
    </div>
</body>
</html>
<?php
}
?>
