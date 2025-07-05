<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Formulir Permohonan Pencairan Dana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 60%;
            margin: auto;
            padding: 20px;
            border: 1px solid #000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn {
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
        <h2>Formulir Permohonan Pencairan Dana</h2>
        <form action="generate.php" method="post">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap">
            </div>
            <div class="form-group">
                <label for="jabatan">Jabatan:</label>
                <input type="text" id="jabatan" name="jabatan">
            </div>
            <div class="form-group">
                <label for="pemberi_bantuan">Pemberi Bantuan:</label>
                <input type="text" id="pemberi_bantuan" name="pemberi_bantuan">
            </div>
            <div class="form-group">
                <label for="judul_bantuan">Judul Bantuan:</label>
                <input type="text" id="judul_bantuan" name="judul_bantuan">
            </div>
            <div class="form-group">
                <label for="npsn">NPSN:</label>
                <input type="text" id="npsn" name="npsn">
            </div>
            <div class="form-group">
                <label for="jenis_lembaga">Jenis Lembaga:</label>
                <input type="text" id="jenis_lembaga" name="jenis_lembaga">
            </div>
            <div class="form-group">
                <label for="alamat_lembaga">Alamat Lembaga:</label>
                <textarea id="alamat_lembaga" name="alamat_lembaga"></textarea>
            </div>
            <div class="form-group">
                <label for="npwp">NPWP:</label>
                <input type="text" id="npwp" name="npwp">
            </div>
            <div class="form-group">
                <label for="nama_bank">Nama Bank:</label>
                <input type="text" id="nama_bank" name="nama_bank">
            </div>
            <div class="form-group">
                <label for="nama_rekening">Nama Rekening:</label>
                <input type="text" id="nama_rekening" name="nama_rekening">
            </div>
            <div class="form-group">
                <label for="nomor_rekening">Nomor Rekening:</label>
                <input type="text" id="nomor_rekening" name="nomor_rekening">
            </div>
            <input type="submit" value="Generate" class="btn">
        </form>
    </div>
</body>
</html>
