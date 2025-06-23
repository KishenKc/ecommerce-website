<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<style>
:root {
    --primary: #666666;          /* grey */
    --background: #f0f0f0;       /* light grey */
    --card-bg: #ffffff;          /* white */
    --text-dark: #222222;        /* dark grey */
    --text-light: #555555;       /* medium grey */
    --accent: #aaaaaa;           /* lighter grey for effects */
    --radius: 14px;
    --shadow: 0 8px 24px rgba(102, 102, 102, 0.3); /* soft grey shadow */
}
body {
    margin: 0;
    background: var(--background);
    color: var(--text-dark);
    font-family: 'Inter', sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
    text-align: center;
}
h1 {
    font-family: 'Playfair Display', serif;
    color: var(--primary);
    font-size: 3rem;
    margin-bottom: 40px;
}
.login-buttons {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 40px;
}
.login-buttons a,
.back-button {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 15px 35px;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
    box-shadow: var(--shadow);
    border: 2px solid var(--primary);
    transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
}
.login-buttons a:hover,
.back-button:hover {
    background: var(--primary);
    color: var(--card-bg);
    box-shadow: 0 0 30px var(--accent);
}
.back-button {
    max-width: 200px;
    margin: 0 auto;
    color: var(--text-light);
    border-color: var(--text-light);
}
.back-button:hover {
    color: var(--card-bg);
    background-color: var(--text-light);
    border-color: var(--text-light);
}
@media (max-width: 500px) {
    .login-buttons {
        flex-direction: column;
        gap: 15px;
    }
}
</style>
</head>
<body>

<h1>Login Portal</h1>
<div class="login-buttons">
    <a href="supplier_login.php">Login as Supplier</a>
    <a href="admin_login.php">Login as Admin</a>
</div>

<a href="index.php" class="back-button">← Back to Home</a>

</body>
</html>
