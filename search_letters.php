<?php
// Security includes
include 'ip_blocker.php';
include 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search C&F Letters by Agent Prefix</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        select { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; } /* Added font-size */
        .btn { display: inline-block; padding: 10px 20px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 16px; }
        a.back-link { color: #007bff; text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Vendor List</a>
        <h1>Search C&F Letters by Agent Prefix</h1>
        <form action="view_letters.php" method="GET">
            <div class="form-group">
                <label for="ref_prefix">Select Agent Prefix:</label>
                <select id="ref_prefix" name="ref_prefix" required>
                    <option value="">-- Select --</option>
                    <option value="TFL/SCM/SE/">Sarothi Enterprise (TFL/SCM/SE/...)</option>
                    <option value="TFL/SCM/THL/">Tea Holdings Limited (TFL/SCM/THL/...)</option>
                    </select>
            </div>
            <button type="submit" class="btn">Search Letters</button>
        </form>
    </div>
</body>
</html>