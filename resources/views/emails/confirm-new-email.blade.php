<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Perubahan Email</title>
</head>
<body>
    <p>Halo, {{ $user->name }}.</p>

    <p>
        Anda mengajukan perubahan alamat email untuk akun SIYANDI menjadi:
        <strong>{{ $user->pending_email }}</strong>
    </p>

    <p>
        Silakan klik tombol di bawah ini untuk mengkonfirmasi perubahan email:
    </p>

    <p>
        <a href="{{ $url }}" style="
            display:inline-block;
            padding:10px 16px;
            background-color:#198754;
            color:#ffffff;
            text-decoration:none;
            border-radius:4px;
            font-weight:bold;
        ">
            Konfirmasi Email Baru
        </a>
    </p>

    <p>
        Jika Anda tidak merasa melakukan perubahan ini, abaikan saja email ini.
        Alamat email akun Anda tidak akan berubah sebelum Anda menekan tombol di atas.
    </p>

    <p>Terima kasih,<br>SIYANDI</p>
</body>
</html>
