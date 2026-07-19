<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Informasi Desa</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background: linear-gradient(135deg, #e0eafc, #cfdef3); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; background: white; width: 100%; max-width: 400px; padding: 40px; }
        .login-icon { font-size: 3rem; color: #4361ee; margin-bottom: 20px; text-align: center; }
        .form-control { border-radius: 10px; padding: 12px 15px; background-color: #f8f9fa; border: 1px solid #e9ecef; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2); border-color: #4361ee; }
        .btn-login { background: linear-gradient(135deg, #4361ee, #3a0ca3); color: white; border-radius: 10px; padding: 12px; font-weight: bold; width: 100%; border: none; transition: 0.3s; }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3); color: white;}
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-icon">
            <i class="fas fa-fingerprint"></i>
        </div>
        <h3 class="text-center fw-bold mb-1">SmartDesa</h3>
        <p class="text-center text-muted mb-4 small">Silakan login untuk mengakses sistem.</p>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal"){
                echo "<div class='alert alert-danger text-center small fw-bold' style='border-radius:10px;'>Login gagal! Username atau Password salah.</div>";
            } else if($_GET['pesan'] == "belum_login"){
                echo "<div class='alert alert-warning text-center small fw-bold' style='border-radius:10px;'>Anda harus login untuk mengakses halaman admin.</div>";
            }
        }
        ?>

        <form action="proses_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold text-dark small">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="username" placeholder="Masukkan username" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold text-dark small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login"><i class="fas fa-sign-in-alt me-2"></i> MASUK</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>