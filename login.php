<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>POS Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background-color: #f8f9fa;
}
.login-card {
  max-width: 400px;
  margin: auto;
  margin-top: 15vh;
}
</style>
</head>
<body>

<div class="card shadow login-card">
  <div class="card-header bg-primary text-white text-center">
    <h4 class="mb-0">POS Login</h4>
  </div>

  <div class="card-body">
    <form method="post" action="auth.php">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button class="btn btn-success w-100">Login</button>
    </form>
  </div>
</div>

</body>
</html>
